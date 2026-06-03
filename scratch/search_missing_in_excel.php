<?php

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$missingIds = ['4992246', '6079209', '4249105', '5243403', '4244359', '6025519', '6134963', '6291139'];

$files = [
    __DIR__ . '/../storage/app/abone_koy_merkez.xlsx',
    __DIR__ . '/../storage/app/abone-gurup-adi.xlsx',
    __DIR__ . '/../storage/app/aboneler_archive.xls'
];

foreach ($files as $file) {
    echo "Kontrol ediliyor: " . basename($file) . "\n";
    $data = Excel::toArray(new class {}, $file);
    $rows = $data[0];

    foreach ($rows as $row) {
        foreach ($row as $cell) {
            if (in_array(trim($cell), $missingIds)) {
                echo "Bulundu in " . basename($file) . ": " . trim($cell) . "\n";
                print_r($row);
            }
        }
    }
}
