<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = '/faturalar/2025/202511.xls';
$faturaNo = '11574598';

$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, false, false);

$headers = array_map(fn($h) => mb_strtoupper(trim((string)$h)), $rows[0]);
$faturaIdx = array_search('FATURA NO', $headers);

if ($faturaIdx === false) {
    echo "Sütunlar: " . implode(' | ', $headers) . "\n";
    die("Fatura No sütunu bulunamadı.\n");
}

$found = false;
foreach($rows as $row) {
    $currentFno = trim((string)($row[$faturaIdx] ?? ''));
    if ($currentFno === $faturaNo) {
        echo "BULUNDU: $faturaNo\n";
        foreach($headers as $idx => $h) {
            $val = $row[$idx];
            if (str_contains($h, 'ENDEKS') || str_contains($h, 'TÜKETİM') || str_contains($h, 'FATURA NO')) {
                echo "  $h: " . var_export($val, true) . "\n";
            }
        }
        $found = true;
        break;
    }
}

if (!$found) {
    echo "Fatura $faturaNo bulunamadı. İlk 5 satır fatura no:\n";
    for($i=1; $i<=5; $i++) {
        echo " - " . ($rows[$i][$faturaIdx] ?? 'EMPTY') . "\n";
    }
}
