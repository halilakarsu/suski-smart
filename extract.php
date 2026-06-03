<?php
require __DIR__ . '/vendor/autoload.php';

$files = glob(__DIR__ . '/public/ornek faturalar/*.xlsx');
foreach ($files as $file) {
    echo "\n--- FILE: " . basename($file) . " ---\n";
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $maxCol = $sheet->getHighestDataColumn();
    
    for ($r = 1; $r <= 3; $r++) {
        $vals = [];
        foreach ($sheet->getRowIterator($r, $r) as $row) {
            foreach ($row->getCellIterator('A', $maxCol) as $cell) {
                $v = $cell->getValue();
                if ($v !== null && $v !== '') $vals[] = trim($v);
            }
        }
        if (count($vals) > 5) {
            print_r($vals);
            break;
        }
    }
}
