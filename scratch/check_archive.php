<?php

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$filePath = __DIR__ . '/../storage/app/aboneler_archive.xls';

try {
    $data = Excel::toArray(new class {}, $filePath);
    $rows = array_slice($data[0], 0, 10);
    print_r($rows);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
