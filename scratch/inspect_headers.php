<?php
require __DIR__.'/vendor/autoload.php';

function print_headers($path) {
    if(!file_exists($path)) {
        echo "File not found: $path\n";
        return;
    }
    echo "--- $path ---\n";
    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        $row = 1;

        // some files might not have headers on row 1, find the first non-empty row or just print row 1..5 loosely
        $highestCol = $worksheet->getHighestColumn();
        $highestColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);

        // find header row (first row with multiple non-empty cells)
        $headerRow = 1;
        for($r=1; $r<=5; $r++) {
            $count = 0;
            for($c=1; $c<=$highestColIdx; $c++) {
                if(!empty($worksheet->getCell([\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c), $r])->getValue())) {
                    $count++;
                }
            }
            if($count > 3) {
                $headerRow = $r;
                break;
            }
        }

        $headers = [];
        for($c=1; $c<=$highestColIdx; $c++) {
             $headers[] = $worksheet->getCell([\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c), $headerRow])->getValue();
        }
        foreach($headers as $h) { if(!empty($h)) echo "$h, "; }
        echo "\n";
    } catch(\Exception $e) {
        echo "Error reading file: " . $e->getMessage() . "\n";
    }
}

print_headers('/Users/akarsu/Desktop/aboneler.xls');
print_headers(__DIR__.'/public/ornek faturalar/202601.xlsx');
