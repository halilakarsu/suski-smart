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

// 1. Load Excel classifications
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

// 2. Query database classifications
$dbAbones = DB::table('aboneler')
    ->whereIn('ABONE_TESIS_NO', array_keys($excelData))
    ->get(['ABONE_TESIS_NO', 'yerlesim_turu'])
    ->keyBy('ABONE_TESIS_NO')
    ->toArray();

echo "DB loaded. Matching subscribers: " . count($dbAbones) . "\n";

$mismatches = [];
$totalChecked = 0;

foreach ($excelData as $tesisat => $excelVal) {
    if (isset($dbAbones[$tesisat])) {
        $dbVal = $dbAbones[$tesisat]->yerlesim_turu;
        
        // Normalize excel val to KÖY or MERKEZ for comparison
        $normalizedExcelVal = ($excelVal === 'KÖY' ? 'KÖY' : 'MERKEZ');
        
        // Normalize db val to case-insensitive or binary comparison
        $normalizedDbVal = strtoupper($dbVal ?? '');
        if ($normalizedDbVal === 'KÖY') {
            $normalizedDbVal = 'KÖY';
        } else {
            $normalizedDbVal = 'MERKEZ';
        }
        
        if ($normalizedExcelVal !== $normalizedDbVal) {
            $mismatches[] = [
                'tesisat' => $tesisat,
                'excel_raw' => $excelVal,
                'excel_norm' => $normalizedExcelVal,
                'db_raw' => $dbVal
            ];
        }
        $totalChecked++;
    }
}

echo "Total compared: $totalChecked\n";
echo "Total mismatches: " . count($mismatches) . "\n";
echo "Mismatch samples (first 30):\n";
foreach (array_slice($mismatches, 0, 30) as $m) {
    echo "  Tesisat: {$m['tesisat']} | Excel: {$m['excel_raw']} ({$m['excel_norm']}) | DB: {$m['db_raw']}\n";
}
