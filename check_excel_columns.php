<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = '/Users/akarsu/Desktop/aboneler.xls';
$spreadsheet = IOFactory::load($file);
$worksheet = $spreadsheet->getActiveSheet();

// Tüm sütunları kontrol et
$headerRow = 3;
echo "=== TÜM EXCEL SÜTUNLARI ===\n\n";

for ($col = 1; $col <= 20; $col++) {
    $value = $worksheet->getCellByColumnAndRow($col, $headerRow)->getValue();
    if ($value) {
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        echo "$colLetter: $value\n";
    }
}

// Toplam satır sayısı
$maxRow = $worksheet->getHighestRow();
echo "\n=== İSTATİSTİKLER ===\n";
echo "Toplam satır: $maxRow\n";
echo "Data satırları (Satır 4+): " . ($maxRow - 3) . "\n";
