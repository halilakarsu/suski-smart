<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = '/faturalar/2020/202004.xls';
$donem = '2020-04';

echo "Dönem Analizi: $donem\n";

$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, false, false);

$headers = array_map(fn($h) => mb_strtoupper(trim((string)$h)), $rows[0]);
$faturaIdx = array_search('FATURA NO', $headers);

$excelFaturaNos = [];
foreach(array_slice($rows, 1) as $row) {
    $fno = trim((string)($row[$faturaIdx] ?? ''));
    if ($fno !== '') $excelFaturaNos[] = $fno;
}

$dbFaturaNos = DB::table('kesinlesen_faturalar')
    ->where('donem', $donem)
    ->pluck('fatura_no')
    ->toArray();

echo "Excel Count: " . count($excelFaturaNos) . "\n";
echo "DB Count: " . count($dbFaturaNos) . "\n";

$missing = array_diff($excelFaturaNos, $dbFaturaNos);
echo "Eksik Fatura Sayısı: " . count($missing) . "\n";
echo "İlk 10 Eksik:\n";
print_r(array_slice($missing, 0, 10));

// Acaba format farkı mı var? (D/ prefixi gibi)
$dbFaturaNosClean = array_map(fn($n) => preg_replace('/[^0-9]/', '', $n), $dbFaturaNos);
$excelFaturaNosClean = array_map(fn($n) => preg_replace('/[^0-9]/', '', $n), $excelFaturaNos);

$missingClean = array_diff($excelFaturaNosClean, $dbFaturaNosClean);
echo "\nTemizlenmiş (Sadece Sayı) Eksik Sayısı: " . count($missingClean) . "\n";
