<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = '/Users/akarsu/Desktop/aboneler.xls';
$spreadsheet = IOFactory::load($file);
$worksheet = $spreadsheet->getActiveSheet();

$headerRow = 3;
echo "=== EXCEL HEADER (Satır 3) ===\n\n";

$headers = [];
for ($col = 1; $col <= 20; $col++) {
    $value = $worksheet->getCellByColumnAndRow($col, $headerRow)->getValue();
    $headers[$col] = $value;
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
    if (!empty($value)) {
        echo "$colLetter: $value\n";
    }
}

echo "\n=== İLK 3 VERİ SATIRI ===\n";
for ($row = 4; $row <= 6; $row++) {
    echo "\n--- Satır $row ---\n";
    for ($col = 1; $col <= 20; $col++) {
        $header = $headers[$col];
        $value = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
        if (!empty($header)) {
            printf("%-35s => %s\n", $header, $value ?? '(boş)');
        }
    }
}
