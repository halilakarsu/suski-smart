<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = '/faturalar/201804.xls';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, false, false); // Formatted = false

echo "İnceleme: $file\n";
echo str_repeat('-', 50) . "\n";

$headers = $rows[0];
$indices = [];
foreach($headers as $idx => $h) {
    if (str_contains(mb_strtolower($h), 'endeks') || str_contains(mb_strtolower($h), 'tüketim') || str_contains(mb_strtolower($h), 'tuketim')) {
        $indices[$idx] = $h;
    }
}

foreach (array_slice($rows, 1, 10) as $rowIndex => $row) {
    echo "Satır " . ($rowIndex + 1) . ":\n";
    foreach ($indices as $idx => $header) {
        $val = $row[$idx];
        echo "  $header: " . (is_null($val) ? 'NULL' : (is_numeric($val) ? var_export($val, true) : $val)) . "\n";
    }
    echo "\n";
}
