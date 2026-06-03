<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = '/faturalar/2026/202601.xls';
$reader = IOFactory::createReaderForFile($file);
$spreadsheet = $reader->load($file);

$sheet = $spreadsheet->getSheet(1); // Sayfa2
$highestCol = $sheet->getHighestColumn();
$highestColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);

for ($c = 1; $c <= $highestColIdx; $c++) {
    $header = $sheet->getCellByColumnAndRow($c, 1)->getValue();
    if ($header) {
        echo "Col $c: $header\n";
    }
}
