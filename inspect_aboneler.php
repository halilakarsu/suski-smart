<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Aboneler;

$file = '/Users/akarsu/Desktop/aboneler.xls';

if (!file_exists($file)) {
    echo "Dosya bulunamadı: $file\n";
    exit(1);
}

try {
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
    
    echo "\n=== EXCEL İÇERİĞİ ===\n";
    echo "Toplam satır: " . count($rows) . "\n\n";
    
    // Header satırını bul (3. satır)
    $headerRow = 2; // index 2 = satır 3
    $header = array_map('strtolower', array_map('trim', $rows[$headerRow]));
    
    echo "Header (Satır 3): " . implode(" | ", $header) . "\n\n";
    
    // Kolon indekslerini bul
    $aboneNoIndex = null;
    $sayacNoIndex = null;
    $ilceIndex = null;
    $adresIndex = null;
    
    foreach ($header as $idx => $col) {
        if (stripos($col, 'abone') !== false && stripos($col, 'tesis') !== false) {
            $aboneNoIndex = $idx;
        }
        if (stripos($col, 'sayac') !== false) {
            $sayacNoIndex = $idx;
        }
        if (stripos($col, 'ilçe') !== false || stripos($col, 'ilce') !== false) {
            $ilceIndex = $idx;
        }
        if (stripos($col, 'adres') !== false) {
            $adresIndex = $idx;
        }
    }
    
    echo "=== KOLON İNDEKSLERİ ===\n";
    echo "Abone Tesis No Index: " . ($aboneNoIndex !== null ? $aboneNoIndex : 'Bulunamadı') . "\n";
    echo "Sayaç No Index: " . ($sayacNoIndex !== null ? $sayacNoIndex : 'Bulunamadı') . "\n";
    echo "İlçe Index: " . ($ilceIndex !== null ? $ilceIndex : 'Bulunamadı') . "\n";
    echo "Adres Index: " . ($adresIndex !== null ? $adresIndex : 'Bulunamadı') . "\n\n";
    
    // Eksikse, elle set et
    if ($aboneNoIndex === null) $aboneNoIndex = 3;
    if ($sayacNoIndex === null) $sayacNoIndex = 5;
    if ($ilceIndex === null) $ilceIndex = 2;
    if ($adresIndex === null) $adresIndex = 7;
    
    echo "=== İLK 20 EXCEL ABONESİ ===\n";
    $excelAbones = [];
    foreach (array_slice($rows, $headerRow + 1, 20) as $idx => $row) {
        if (!empty($row[$aboneNoIndex])) {
            $aboneNo = trim((string)$row[$aboneNoIndex]);
            $sayacNo = trim((string)($row[$sayacNoIndex] ?? ''));
            $ilce = trim((string)($row[$ilceIndex] ?? ''));
            $adres = trim((string)($row[$adresIndex] ?? ''));
            
            echo ($idx + 1) . ". Abone: {$aboneNo} | Sayaç: {$sayacNo} | İlçe: {$ilce}\n";
            $excelAbones[$aboneNo] = [
                'sayac' => $sayacNo,
                'ilce' => $ilce,
                'adres' => $adres
            ];
        }
    }
    
    // Veritabanında kontrol et
    echo "\n=== VERİTABANI ABONELERİ KARŞILAŞTIRMASI ===\n";
    $dbAbones = Aboneler::whereIn('ABONE_TESIS_NO', array_keys($excelAbones))->get(['ABONE_TESIS_NO', 'SAYAC_SERI_NO', 'BOLGE_ADI', 'ADRES']);
    
    $found = [];
    $missing = [];
    
    foreach ($excelAbones as $aboneNo => $data) {
        $dbAbone = $dbAbones->firstWhere('ABONE_TESIS_NO', $aboneNo);
        if ($dbAbone) {
            $found[] = $aboneNo;
            $sayacMatch = trim($dbAbone->SAYAC_SERI_NO) === $data['sayac'];
            $icon = $sayacMatch ? '✓' : '⚠';
            echo $icon . " Bulundu: {$aboneNo} | DB Sayaç: {$dbAbone->SAYAC_SERI_NO} | Excel Sayaç: {$data['sayac']}\n";
        } else {
            $missing[] = $aboneNo;
            echo "✗ EKSİK: {$aboneNo}\n";
        }
    }
    
    echo "\n=== ÖZET ===\n";
    echo "Toplam Excel Abonesi (kontrol edilen): " . count($excelAbones) . "\n";
    echo "Veritabanında Bulunan: " . count($found) . "\n";
    echo "Eksik Olan: " . count($missing) . "\n";
    
    if (!empty($missing)) {
        echo "\nEksik aboneler: " . implode(", ", $missing) . "\n";
    }
    
} catch (\Exception $e) {
    echo "Hata: " . $e->getMessage() . "\n";
}
?>
