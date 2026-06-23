<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

$excelPath = __DIR__ . '/../storage/app/abone_koy_merkez.xlsx';
if (!file_exists($excelPath)) {
    die("Excel file not found!\n");
}

// 1. Get database subscribers in group '7.Mesken KÖİ' with yerlesim_turu = 'MERKEZ'
$dbMismatches = DB::table('aboneler')
    ->where('abone_grubu', '7.Mesken KÖİ')
    ->where('yerlesim_turu', 'MERKEZ')
    ->pluck('yerlesim_turu', 'ABONE_TESIS_NO')
    ->toArray();

echo "DB Mismatches count: " . count($dbMismatches) . "\n";

// 2. Read Excel file
echo "Reading Excel...\n";
$data = Excel::toArray(new class {}, $excelPath);
$rows = $data[0];

$excelData = [];
foreach ($rows as $idx => $row) {
    if ($idx === 0) continue;
    $tesisat = trim($row[1] ?? '');
    if (!$tesisat) continue;
    $excelData[$tesisat] = trim($row[3] ?? '');
}

echo "Excel loaded. Total rows: " . count($excelData) . "\n";

$matchedInExcel = 0;
$mismatchDetails = [];

foreach ($dbMismatches as $tesisat => $dbVal) {
    if (isset($excelData[$tesisat])) {
        $excelVal = $excelData[$tesisat];
        if ($excelVal !== $dbVal) {
            $mismatchDetails[] = "Tesisat: $tesisat | DB: $dbVal | Excel: $excelVal";
        }
        $matchedInExcel++;
    } else {
        $mismatchDetails[] = "Tesisat: $tesisat | DB: $dbVal | Excel: NOT FOUND";
    }
}

echo "Matched in Excel: $matchedInExcel / " . count($dbMismatches) . "\n";
echo "Discrepancy list (first 20):\n";
print_r(array_slice($mismatchDetails, 0, 20));
