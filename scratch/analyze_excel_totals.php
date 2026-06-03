<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'public/Şuski 202603 data.xlsx';
if (!file_exists($file)) {
    echo "File not found: $file\n";
    exit;
}

$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);

$header = array_shift($rows);
echo "Headers:\n";
print_r($header);

$total = 0;
$count = 0;
// We'll look for 'GENEL TOPLAM' or 'ÖDENECEK TUTAR' or similar
$colKey = null;
foreach ($header as $key => $val) {
    $valUpper = mb_strtoupper($val, 'UTF-8');
    if (str_contains($valUpper, 'GENEL TOPLAM') || str_contains($valUpper, 'ÖDENECEK') || str_contains($valUpper, 'TUTAR')) {
        $colKey = $key;
        echo "Found potential total column: $val ($key)\n";
    }
}

if ($colKey) {
    foreach ($rows as $row) {
        $val = $row[$colKey];
        if (is_numeric($val)) {
            $total += (float)$val;
            $count++;
        }
    }
    echo "\nExcel Summary for $file:\n";
    echo "Total Rows with data: $count\n";
    echo "Sum of $header[$colKey]: $total\n";
} else {
    echo "Could not find a total column.\n";
}
