<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = base_path('storage/app/temp_imports/202008.xls');

echo "Reading headers from $filePath...\n";

$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, false);

if (!empty($rows)) {
    echo "Headers:\n";
    print_r($rows[0]);
}
