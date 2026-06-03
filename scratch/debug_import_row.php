<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

// I'll copy the logic from import_gecmis_faturalar.php
$file = '/faturalar/2020/202004.xls';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$row = 2; // First data row

$highestCol = $sheet->getHighestColumn();
$highestColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);

$columnMapping = [
    'SIRA'                          => 'sira_no',
    'FATURA NO'                     => 'fatura_no',
    'TESİSAT NO'                    => 'tesisat_no',
];

$excelColMap = [];
for ($c = 1; $c <= $highestColIdx; $c++) {
    $raw    = $sheet->getCellByColumnAndRow($c, 1)->getValue();
    $header = mb_strtoupper(trim((string)($raw ?? '')));
    $dbCol = $columnMapping[$raw] ?? $columnMapping[$header] ?? null;
    if ($dbCol) $excelColMap[$c] = $dbCol;
}

echo "Excel Col Map: "; print_r($excelColMap);

$data = [];
foreach ($excelColMap as $c => $dbCol) {
    $value = $sheet->getCellByColumnAndRow($c, $row)->getCalculatedValue();
    $data[$dbCol] = $value;
}

echo "Data Array: "; print_r($data);
