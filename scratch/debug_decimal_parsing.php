<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function parseDecimalMock($value)
{
    $value = trim((string) $value);
    if ($value === '')
        return null;
    $value = str_replace(' ', '', $value);

    if (str_contains($value, '.') && str_contains($value, ',')) {
        if (strrpos($value, '.') > strrpos($value, ',')) {
            $value = str_replace(',', '', $value);
        } else {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
    } elseif (str_contains($value, ',')) {
        $value = str_replace(',', '.', $value);
    } else {
        // Just dot or none.
    }

    $clean = preg_replace('/[^0-9.\-]/', '', $value);
    return is_numeric($clean) ? (float) $clean : null;
}

$file = 'public/Şuski 202603 data.xlsx';
if (!file_exists($file)) {
    die("File not found\n");
}

$reader = IOFactory::createReaderForFile($file);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);

$header = array_shift($rows);
$colKey = null;
foreach ($header as $key => $val) {
    if (str_contains(mb_strtoupper($val, 'UTF-8'), 'TOPLAM')) {
        $colKey = $key;
        echo "Analyzing column: $val ($key)\n";
    }
}

if (!$colKey)
    die("No total column found\n");

echo str_pad("Raw Excel", 20) . " | " . str_pad("Parsed", 15) . " | Status\n";
echo str_repeat("-", 50) . "\n";

$limit = 20;
$i = 0;
foreach ($rows as $row) {
    $raw = $row[$colKey];
    if ($raw === null || $raw === '')
        continue;

    $parsed = parseDecimalMock($raw);
    $status = "";

    // Check if original string look like 1.500 (one thousand five hundred)
    // but parsed as 1.5
    if (str_contains($raw, '.') && !str_contains($raw, ',')) {
        $parts = explode('.', $raw);
        if (isset($parts[1]) && strlen($parts[1]) === 3) {
            $status = "POTENTIAL ERROR (1000x smaller?)";
        }
    }

    echo str_pad((string) $raw, 20) . " | " . str_pad((string) $parsed, 15) . " | $status\n";

    $i++;
    if ($i >= $limit)
        break;
}
