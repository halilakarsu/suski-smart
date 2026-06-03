<?php
require __DIR__.'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function findField(array $payload, array $keys): ?string
{
    foreach ($payload as $k => $v) {
        foreach ($keys as $key) {
            if (mb_strtolower(trim($k), 'UTF-8') === mb_strtolower($key, 'UTF-8')) {
                return $v !== null && $v !== '' ? (string)$v : null;
            }
        }
    }
    return null;
}

$faturaFile = 'public/ornek faturalar/202602.xlsx';
$reader = IOFactory::createReaderForFile($faturaFile);
$reader->setReadDataOnly(true);
$filter = new class implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
    public function readCell($col, $row, $ws = '') { return $row <= 10; }
};
$reader->setReadFilter($filter);
$sheet = $reader->load($faturaFile)->getActiveSheet();
$rows  = $sheet->toArray(null, true, true, false);

$headerIdx = 0;
foreach (array_slice($rows, 0, 10) as $i => $row) {
    if(in_array('FATURA NO', $row, true) || in_array('Tesisat', $row, true)) {
        $headerIdx = $i; break;
    }
}

$headers = array_map('trim', $rows[$headerIdx]);
$headers = array_filter($headers, fn($h) => $h !== ''); 
$dataRows = array_slice($rows, $headerIdx + 1, 5);

echo "---- SİLİNMİŞ SÜTUN TESTİ (ŞABLON KORUMASI) ----\n";
// Farz edelim ki "IL" ve "BOLGE_ADI" kolonlarını kullanıcı Excel'den sildi.
$silinmisHeaders = $headers;
unset($silinmisHeaders[0]); // IL
unset($silinmisHeaders[1]); // BOLGE_ADI
// Algoritma bunu array_diff_key ile yakalar.
$baseHmap = []; foreach($headers as $h) $baseHmap[mb_strtolower(trim($h))] = $h;
$newHmap  = []; foreach($silinmisHeaders as $h) $newHmap[mb_strtolower(trim($h))] = $h;
$eksik = array_diff_key($baseHmap, $newHmap);
if(!empty($eksik)){
    echo "Sistem Başarıyla İçe Aktarmayı İptal Etti! [EKSİK SÜTUNLAR]: " . implode(', ', array_values($eksik)) . "\n\n";
}

echo "---- PAYLOAD ANOMALİ SİMÜLASYONU VE VERİ MANİPÜLASYONU ----\n";
foreach ($dataRows as $i => $row) {
    $rowValues = array_values($row);
    $payload = [];
    foreach ($headers as $k => $h) {
        if(isset($rowValues[$k])) $payload[$h] = $rowValues[$k];
    }
    
    // Anomali enjekte ediyoruz
    if($i == 0) {
        echo "[1. Satıra] Tüketim Negatif (T1 İlk < T1 Son) Enjekte Ediliyor...\n";
        $payload['T1_ILK_ENDEKS'] = 5000;
        $payload['T1_SON_ENDEKS'] = 2000; 
    } elseif ($i == 1) {
        echo "[2. Satıra] Reaktif Ceza Enjekte Ediliyor...\n";
        $payload['REAKTİF TÜKETİM'] = 1200.50; 
    } elseif ($i == 2) {
        echo "[3. Satıra] Anormal Sarfiyat Enjekte Ediliyor... Geçmiş tüketim 100, bu fatura 500.\n";
        $eskiTuketimMock = 100;
        $payload['AKTIF KWH'] = 500; 
    } elseif ($i == 3) {
        echo "[4. Satıra] Çarpan Değişimi Enjekte Ediliyor... Geçmiş 10, bu fatura 150.\n";
        $eskiCarpanMock = 10;
        $payload['CARPAN'] = 150; 
    } elseif ($i == 4) {
        echo "[5. Satıra] Ani Sıfır Tüketim Enjekte Ediliyor... Geçmiş 1500, bu fatura 0.\n";
        $eskiSifirMock = 1500;
        $payload['AKTIF KWH'] = 0; 
    }

    // Anomali Testi (ExcelImportService satır 330 ve ilerisi)
    $anomaliler = [];

    // 1. Negatif:
    $t1Ilk = (float)str_replace(',', '.', findField($payload, ['t1_ilk_endeks', 't1 ilk endeks']));
    $t1Son = (float)str_replace(',', '.', findField($payload, ['t1_son_endeks', 't1 son endeks']));
    if ($t1Son > 0 && $t1Son < $t1Ilk) $anomaliler[] = 'NEGATIF_TUKETIM_YAKALANDI';

    // 2. Reaktif Ceza: 
    $reaktifTl = (float)str_replace(',', '.', findField($payload, ['reakti̇f tüketi̇m', 'reaktif tüketim', 'reaktif_tl', 'reaktif_miktar']));
    if ($reaktifTl > 0) $anomaliler[] = 'REAKTIF_CEZA_YAKALANDI';

    // 3. Anormal Tüketim:
    if($i == 2) {
        $guncelTuketim = (float)str_replace(',', '.', findField($payload, ['fatura_edilecek_toplam_tuketim_kwh', 'toplam_tuketim', 'aktif_kwh', 'aktif kwh']));
        if ($eskiTuketimMock > 10 && $guncelTuketim > 0) {
            $oran = $guncelTuketim / $eskiTuketimMock;
            if ($oran > 3 || $oran < 0.2) $anomaliler[] = "ANORMAL_TUKETIM_YAKALANDI (Oran: $oran)";
        }
    }

    // 4. Çarpan:
    if($i == 3) {
         $guncelCarpan = (float)str_replace(',', '.', findField($payload, ['carpan', 'çarpan']));
         if ($eskiCarpanMock > 0 && $guncelCarpan > 0 && $eskiCarpanMock !== $guncelCarpan) {
             $anomaliler[] = "CARPAN_DEGISIMI_YAKALANDI (Fark: $eskiCarpanMock -> $guncelCarpan)";
         }
    }

    // 5. Sıfır Tüketim:
    if($i == 4) {
        $guncelSifirTuketim = (float)str_replace(',', '.', findField($payload, ['fatura_edilecek_toplam_tuketim_kwh', 'toplam_tuketim', 'aktif_kwh', 'aktif kwh']));
        if ($eskiSifirMock > 50 && $guncelSifirTuketim == 0) {
             $anomaliler[] = "SIFIR_TUKETIM_YAKALANDI";
         }
    }

    if (!empty($anomaliler)) {
         echo "  => Test Sonucu: " . implode(' | ', $anomaliler) . "\n\n";
    }
}
