<?php
chdir('/var/www/html');
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ImportLog;
use PhpOffice\PhpSpreadsheet\IOFactory;

$importLog = ImportLog::find(68);
if (!$importLog) {
    die("Import log 68 not found\n");
}

$fullPath = storage_path('app/private/' . $importLog->yol);
if (!file_exists($fullPath)) {
    die("File not found at: $fullPath\n");
}

echo "Loading Excel file...\n";
$reader = IOFactory::createReaderForFile($fullPath);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($fullPath);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, false, false);

// Find header row (same dynamic logic as service)
$headerRowOffset = 0;
$bulundu = false;
foreach (array_slice($rows, 0, 50) as $rowIndex => $row) {
    $cleanRow = array_map(function ($h) {
        return preg_replace('/[^a-z0-9_]/u', '', mb_strtolower(trim((string) $h), 'UTF-8'));
    }, $row);

    $eslesenSayisi = 0;
    foreach ($cleanRow as $h) {
        if (str_contains($h, 'fatura') || str_contains($h, 'tesisat') || str_contains($h, 'okuma')) {
            $eslesenSayisi++;
        }
    }

    if ($eslesenSayisi >= 2) {
        $headerRowOffset = $rowIndex;
        $bulundu = true;
        break;
    }
}

if (!$bulundu) {
    die("Header row not found\n");
}

$headers = array_map(fn ($h) => trim((string) $h), $rows[$headerRowOffset]);
$headers = array_filter($headers, fn ($h) => $h !== ''); // Sadece dolu başlıkları al

$normalize = fn ($h) => mb_strtolower(trim((string) $h), 'UTF-8');
$yoksayilanTemiz = array_map($normalize, config('excel_import.yoksayilan'));

$headersMetadata = [];
foreach ($headers as $idx => $h) {
    $headersMetadata[$idx] = ! in_array($normalize($h), $yoksayilanTemiz);
}

$dataRows = array_slice($rows, $headerRowOffset + 1);
echo "Processing " . count($dataRows) . " rows...\n";

function findField(array $payload, array $keys)
{
    foreach ($payload as $k => $v) {
        foreach ($keys as $key) {
            if (mb_strtolower(trim((string) $k), 'UTF-8') === mb_strtolower($key, 'UTF-8')) {
                $val = $v !== null ? trim((string) $v) : null;
                return ($val !== '') ? $val : null;
            }
        }
    }
    return null;
}

function parseDecimal($value): ?string
{
    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    $value = str_replace(' ', '', $value);

    if (str_contains($value, '.') && str_contains($value, ',')) {
        if (strrpos($value, '.') > strrpos($value, ',')) {
            $value = str_replace(',', '', $value);
        } else {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
    }
    elseif (str_contains($value, ',')) {
        $value = str_replace(',', '.', $value);
    }

    $clean = preg_replace('/[^0-9.\-]/', '', $value);

    return is_numeric($clean) ? (string) $clean : null;
}

$updatedCount = 0;
$siraNoCount = 1;

$models = [
    \App\Models\KesinlesenFatura::class,
    \App\Models\BeklemeKontrolHavuzu::class,
    \App\Models\Reaktifler::class,
    \App\Models\ItirazEdilenler::class,
];

foreach ($dataRows as $row) {
    $filtered = array_filter($row, fn ($v) => $v !== null && $v !== '');
    if (empty($filtered)) {
        continue;
    }

    $rowValues = array_values($row);
    $payload = ['SIRA NO' => $siraNoCount];

    foreach ($headers as $k => $h) {
        if (! empty($headersMetadata[$k]) && isset($rowValues[$k])) {
            $key = $h;
            $suffix = 1;
            while (array_key_exists($key, $payload)) {
                $suffix++;
                $key = $h.'_'.$suffix;
            }
            $payload[$key] = $rowValues[$k];
        }
    }

    $siraNoCount++;

    // Find tesisat_no and fatura_no from payload
    $tesisatNo = findField($payload, ['tesisat', 'tesisat no', 'abone_tesis_no']);
    $faturaNo = findField($payload, ['fatura no', 'fatura_no', 'faturano']);

    if (!$tesisatNo && !$faturaNo) continue;

    // Process each model type
    foreach ($models as $modelClass) {
        $records = $modelClass::where('import_log_id', 68)
            ->where(function($q) use ($tesisatNo, $faturaNo) {
                if ($tesisatNo) {
                    $q->where('tesisat_no', $tesisatNo);
                }
                if ($faturaNo) {
                    $q->orWhere('fatura_no', $faturaNo);
                }
            })
            ->get();

        foreach ($records as $record) {
            // Parse birim_fiyat using the new logic
            $bestVal = null;
            foreach ($payload as $k => $v) {
                $kNorm = str_replace([' ', 'ı', 'İ', 'ß'], ['_', 'i', 'i', 'ss'], mb_strtolower(trim((string)$k), 'UTF-8'));
                if (!preg_match('/^birim_fiyat(_\d+)?$/', $kNorm)) continue;
                if (is_array($v)) continue;
                $dec = parseDecimal($v);
                if ($dec !== null && (float)$dec > 0) {
                    $bestVal = $dec;
                    break;
                }
            }
            if ($bestVal === null) {
                $bestVal = parseDecimal(
                    findField($payload, ['t1_birim_fiyat', 't1 birim fiyat'])
                    ?? findField($payload, ['t2_birim_fiyat', 't2 birim fiyat'])
                    ?? findField($payload, ['t3_birim_fiyat', 't3 birim fiyat'])
                );
            }

            // Update fatura record
            $record->birim_fiyat = $bestVal ?? 0;
            $record->payload = $payload;
            $record->save();
            $updatedCount++;
        }
    }
}

echo "Successfully updated $updatedCount records across tables!\n";
