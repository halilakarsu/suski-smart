<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class HeaderFilter implements IReadFilter {
    public function readCell($columnAddress, $row, $worksheetName = '') {
        return $row <= 10;
    }
}

$file = '/Users/akarsu/Desktop/aboneler.xls';

echo "Inspecting $file with filter...\n";
try {
    $reader = IOFactory::createReaderForFile($file);
    $reader->setReadDataOnly(true);
    $reader->setReadFilter(new HeaderFilter());
    
    $spreadsheet = $reader->load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, false);
    
    if (!empty($rows)) {
        echo "Headers found (First Row):\n";
        echo implode(' | ', $rows[0]) . "\n";
        
        echo "\nData rows (First 5):\n";
        foreach (array_slice($rows, 1, 5) as $idx => $row) {
            echo "Row " . ($idx + 1) . ": " . implode(' | ', $row) . "\n";
        }
    } else {
        echo "File read but no rows found.\n";
    }
} catch (\Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}
