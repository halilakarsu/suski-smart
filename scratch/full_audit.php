<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

$baseDir = '/faturalar';
$years = [2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024, 2025, 2026];

echo str_repeat('=', 60) . "\n";
echo "📊 VERİ DENETİM RAPORU (Excel vs Veritabanı)\n";
echo str_repeat('=', 60) . "\n";
echo str_pad("Dönem", 10) . " | " . str_pad("Excel", 10) . " | " . str_pad("DB", 10) . " | " . "Durum\n";
echo str_repeat('-', 60) . "\n";

foreach ($years as $year) {
    $yearDir = "$baseDir/$year";
    if (!is_dir($yearDir)) continue;

    $files = glob($yearDir . '/*.xls*');
    sort($files);

    foreach ($files as $file) {
        $filename = basename($file);
        preg_match('/(\d{4})(\d{2})/', $filename, $m);
        if (!$m) continue;
        
        $donem = $m[1] . '-' . $m[2];
        
        // Excel satır sayısını al (Başlık hariç)
        try {
            $reader = IOFactory::createReaderForFile($file);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $excelCount = $sheet->getHighestRow() - 1;
            
            // DB sayısını al
            $dbCount = DB::table('kesinlesen_faturalar')->where('donem', $donem)->count();
            
            $status = ($excelCount == $dbCount) ? "✅ UYUMLU" : "❌ FARK VAR (" . ($excelCount - $dbCount) . ")";
            
            echo str_pad($donem, 10) . " | " . str_pad($excelCount, 10) . " | " . str_pad($dbCount, 10) . " | " . $status . "\n";
            
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            gc_collect_cycles();
        } catch (\Exception $e) {
            echo str_pad($donem, 10) . " | " . "HATA: " . substr($e->getMessage(), 0, 30) . "\n";
        }
    }
}
