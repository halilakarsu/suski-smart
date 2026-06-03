<?php
require 'vendor/autoload.php';

// ── 1. Fatura dosyasını oku ───────────────────────────────────────────────────
$faturaFile = 'public/ornek faturalar/202602.xlsx';
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($faturaFile);
$reader->setReadDataOnly(true);

$filter = new class implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
    public function readCell($col, $row, $ws = '') { return $row <= 8; }
};
$reader->setReadFilter($filter);
$sheet = $reader->load($faturaFile)->getActiveSheet();
$rows  = $sheet->toArray(null, true, true, false);

// Başlık satırını bul
$headerRow  = [];
$headerIdx  = 0;
foreach (array_slice($rows, 0, 15) as $i => $row) {
    $clean = array_map(function($h) {
        return preg_replace('/[^a-z0-9_]/u', '', mb_strtolower(trim((string)$h), 'UTF-8'));
    }, $row);
    $hit = 0;
    foreach ($clean as $h) {
        if (str_contains($h,'fatura') || str_contains($h,'tesisat') || str_contains($h,'okuma')) $hit++;
    }
    if ($hit >= 2) { $headerRow = $row; $headerIdx = $i; break; }
}

echo "=== FATURA DOSYASI — İLK 5 VERİ SATIRI ===\n\n";
$dataRows = array_slice($rows, $headerIdx + 1, 5);
foreach ($dataRows as $rowNum => $dataRow) {
    echo "--- Satır " . ($rowNum + 1) . " ---\n";
    foreach ($headerRow as $col => $header) {
        $header = trim((string)$header);
        $value  = trim((string)($dataRow[$col] ?? ''));
        if ($header !== '' && $value !== '') {
            echo sprintf("  %-35s => %s\n", "[$header]", $value);
        }
    }
    echo "\n";
}

// ── 2. Kontrol havuzu dosyasını oku ──────────────────────────────────────────
echo "\n=== KONTROL HAVUZU (masaüstü) — BAŞLIKLAR VE İLK SATIR ===\n\n";
$havuzFile = '/Users/akarsu/Desktop/konttrol-havuzu.xls';
if (file_exists($havuzFile)) {
    $reader2 = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($havuzFile);
    $reader2->setReadDataOnly(true);
    $filter2 = new class implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
        public function readCell($col, $row, $ws = '') { return $row <= 4; }
    };
    $reader2->setReadFilter($filter2);
    $sheet2 = $reader2->load($havuzFile)->getActiveSheet();
    $rows2  = $sheet2->toArray(null, true, true, false);
    
    $hrow = $rows2[0] ?? [];
    echo "Başlıklar:\n";
    foreach ($hrow as $col => $h) {
        $h = trim((string)$h);
        if ($h !== '') echo "  $col: [$h]\n";
    }
    
    $drow = $rows2[1] ?? [];
    echo "\nİlk veri satırı:\n";
    foreach ($hrow as $col => $h) {
        $h = trim((string)$h);
        $v = trim((string)($drow[$col] ?? ''));
        if ($h !== '') echo sprintf("  %-30s => %s\n", "[$h]", $v);
    }
} else {
    echo "Dosya bulunamadı: $havuzFile\n";
}
