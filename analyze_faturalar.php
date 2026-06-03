<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

echo "=== FATURA DOSYALARI ANALIZI ===\n\n";

$faturaDir = 'public/ornek faturalar';
$files = glob("$faturaDir/*.xlsx");

foreach ($files as $file) {
    $basename = basename($file);
    echo "📄 $basename\n";
    echo str_repeat("-", 60) . "\n";
    
    try {
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Başlıkları bul
        $headerRow = null;
        for ($row = 1; $row <= 10; $row++) {
            $cell1 = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            if (strpos(strtolower($cell1), 'fatura') !== false || 
                strpos(strtolower($cell1), 'tesisat') !== false) {
                $headerRow = $row;
                break;
            }
        }
        
        if ($headerRow) {
            echo "Header satırı: $headerRow\n\n";
            
            // Header'ları yazdır
            echo "Başlıklar:\n";
            $headers = [];
            for ($col = 1; $col <= 15; $col++) {
                $value = $worksheet->getCellByColumnAndRow($col, $headerRow)->getValue();
                if (!empty($value)) {
                    $headers[$col] = $value;
                    echo "  Kolon " . chr(64 + $col) . ": $value\n";
                }
            }
            
            // İlk veri satırı
            echo "\nİlk veri satırı:\n";
            for ($col = 1; $col <= 15; $col++) {
                if (isset($headers[$col])) {
                    $value = $worksheet->getCellByColumnAndRow($col, $headerRow + 1)->getValue();
                    printf("  %-25s => %s\n", $headers[$col], $value ?? '(boş)');
                }
            }
        }
    } catch (Exception $e) {
        echo "Hata: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}
