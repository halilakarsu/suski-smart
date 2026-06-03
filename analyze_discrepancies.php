<?php
/**
 * Veritabanı, Excel ve Faturalar arasındaki tutarsızlıkları analiz et
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\Aboneler;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

echo "════════════════════════════════════════════════════════════\n";
echo "  TUTARSIZLIK ANALİZİ: Veritabanı vs Excel vs Faturalar\n";
echo "════════════════════════════════════════════════════════════\n\n";

// ============ 1. VERİTABANINDAN ABONE LİSTESİ ============
echo "1️⃣  VERİTABANINDAN ABONELER\n";
echo "─────────────────────────────────────────────────────────\n";

$dbAboneler = Aboneler::select('ABONE_TESIS_NO', 'PMUM', 'SAYAC_SERI_NO', 'hesap_adi', 'abone_grubu', 'tarife', 'OG_durumu', 'baglanti_grubu', 'tesis_cinsi', 'BOLGE_ADI', 'ADRES', 'guncelleme_tarihi')->get()->keyBy('ABONE_TESIS_NO');

echo "   ✓ Toplam abone: " . $dbAboneler->count() . "\n";
echo "   • Eksik hesap_adi: " . $dbAboneler->whereNull('hesap_adi')->count() . "\n";
echo "   • Eksik abone_grubu: " . $dbAboneler->whereNull('abone_grubu')->count() . "\n";
echo "   • Eksik tarife: " . $dbAboneler->whereNull('tarife')->count() . "\n";
echo "   • Eksik OG_durumu: " . $dbAboneler->whereNull('OG_durumu')->count() . "\n";
echo "   • Eksik baglanti_grubu: " . $dbAboneler->whereNull('baglanti_grubu')->count() . "\n\n";

// ============ 2. EXCEL'DEN ABONE LİSTESİ ============
echo "2️⃣  EXCEL'DEN ABONELER\n";
echo "─────────────────────────────────────────────────────────\n";

$excelFile = '/Users/akarsu/Desktop/aboneler.xls';
if (!file_exists($excelFile)) {
    echo "   ✗ Excel dosyası bulunamadı: $excelFile\n";
    exit;
}

try {
    $spreadsheet = IOFactory::load($excelFile);
    $sheet = $spreadsheet->getActiveSheet();
    $excelAboneler = [];
    
    // A5'ten başlayarak oku (header'ı geç)
    $row = 5;
    while ($row <= $sheet->getHighestRow()) {
        $aboneTesisNo = trim((string)$sheet->getCell('D' . $row)->getValue());
        
        if (empty($aboneTesisNo)) {
            $row++;
            continue;
        }
        
        $excelAboneler[$aboneTesisNo] = [
            'ABONE_TESIS_NO' => $aboneTesisNo,
            'SIRA' => $sheet->getCell('B' . $row)->getValue(),
            'ILCESI' => trim((string)$sheet->getCell('C' . $row)->getValue()),
            'PMUM' => $sheet->getCell('E' . $row)->getValue(),
            'SAYAC_NO' => trim((string)$sheet->getCell('F' . $row)->getValue()),
            'SOZLESME_GUCU' => $sheet->getCell('G' . $row)->getValue(),
            'ADRES' => trim((string)$sheet->getCell('H' . $row)->getValue()),
        ];
        
        $row++;
    }
    
    echo "   ✓ Toplam abone: " . count($excelAboneler) . "\n\n";
} catch (\Exception $e) {
    echo "   ✗ Excel okunamadı: " . $e->getMessage() . "\n";
    exit;
}

// ============ 3. FATURALARDAN ABONE LİSTESİ ============
echo "3️⃣  FATURALARIN ABONE BİLGİLERİ\n";
echo "─────────────────────────────────────────────────────────\n";

$faturaAboneler = [];
$faturaDir = '/Users/akarsu/Desktop/suski/public/ornek faturalar';

if (is_dir($faturaDir)) {
    $files = glob($faturaDir . '/*.xlsx');
    echo "   Tarama dosyaları: " . count($files) . "\n";
    
    foreach ($files as $file) {
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            
            // Faturalardaki sayaç ve abone no bilgisini bul
            $aboneTesisNo = null;
            $sayacNo = null;
            
            // Satır satır tara
            for ($row = 1; $row <= min(50, $sheet->getHighestRow()); $row++) {
                $cellValue = (string)$sheet->getCell('A' . $row)->getValue();
                
                // "SAYAC NO:" veya "Sayac No:" şeklinde aranacak
                if (stripos($cellValue, 'SAYAC') !== false && stripos($cellValue, 'NO') !== false) {
                    $sayacNo = trim(str_ireplace(['SAYAC', 'NO:', 'NO.'], '', $cellValue));
                }
                
                // "ABONE TESIS NO:" veya benzeri aranacak
                if (stripos($cellValue, 'ABONE') !== false && stripos($cellValue, 'TESIS') !== false) {
                    $aboneTesisNo = trim(str_ireplace(['ABONE', 'TESIS', 'NO:', 'NO.'], '', $cellValue));
                }
            }
            
            if ($aboneTesisNo && $sayacNo) {
                if (!isset($faturaAboneler[$aboneTesisNo])) {
                    $faturaAboneler[$aboneTesisNo] = [
                        'ABONE_TESIS_NO' => $aboneTesisNo,
                        'SAYAC_NO' => $sayacNo,
                        'dosya' => basename($file),
                        'tarih' => filemtime($file),
                    ];
                }
            }
        } catch (\Exception $e) {
            // Hata atla
        }
    }
}

echo "   ✓ Faturada bulunan abone: " . count($faturaAboneler) . "\n\n";

// ============ 4. KARŞILAŞTIRMA ============
echo "4️⃣  KARŞILAŞTIRMA VE TUTARSIZLIKLAR\n";
echo "─────────────────────────────────────────────────────────\n";

// Excel'de olup DB'de olmayan
$excelDeBulunanDbDeBulunmayan = array_diff_key($excelAboneler, $dbAboneler);
echo "   • Excel'de olup VT'de olmayan: " . count($excelDeBulunanDbDeBulunmayan) . " abone\n";

// DB'de olup Excel'de olmayan
$dbDeBulunanExcelDeBulunmayan = array_diff_key($dbAboneler, $excelAboneler);
echo "   • VT'de olup Excel'de olmayan: " . count($dbDeBulunanExcelDeBulunmayan) . " abone\n";

// Her iki yerde de olan (karşılaştırma)
$ortakAboneler = array_intersect_key($dbAboneler, $excelAboneler);
echo "   • Her iki kaynakta da olan: " . count($ortakAboneler) . " abone\n";

// Faturalardaki aboneler
$faturaVeDbDeFarklı = [];
foreach ($faturaAboneler as $no => $fatura) {
    if (!isset($dbAboneler[$no])) {
        $faturaVeDbDeFarklı[$no] = $fatura;
    }
}
echo "   • Faturada olup VT'de olmayan: " . count($faturaVeDbDeFarklı) . " abone\n\n";

// ============ 5. ÖRNEK VERİLER ============
echo "5️⃣  ÖRNEK VERİLER\n";
echo "─────────────────────────────────────────────────────────\n";

echo "\n   Excel'de olup VT'de OLMAYAN (ilk 5):\n";
$count = 0;
foreach ($excelDeBulunanDbDeBulunmayan as $no => $abone) {
    if ($count++ >= 5) break;
    echo "      • $no | Sayaç: {$abone['SAYAC_NO']} | Adres: {$abone['ADRES']}\n";
}

echo "\n   VT'de olup Excel'de OLMAYAN (ilk 5):\n";
$count = 0;
foreach ($dbDeBulunanExcelDeBulunmayan as $no => $abone) {
    if ($count++ >= 5) break;
    echo "      • $no | Sayaç: {$abone['SAYAC_SERI_NO']} | Adres: {$abone['ADRES']}\n";
}

echo "\n   Eksik ALAN örneği (hesap_adi=null) (ilk 5):\n";
$count = 0;
foreach ($dbAboneler as $abone) {
    if ($count++ >= 5) break;
    if (is_null($abone['hesap_adi'])) {
        echo "      • {$abone['ABONE_TESIS_NO']} | hesap_adi: " . ($abone['hesap_adi'] ?: 'NULL') . " | tesis_cinsi: {$abone['tesis_cinsi']}\n";
    }
}

echo "\n════════════════════════════════════════════════════════════\n";
echo "  ✓ Analiz tamamlandı. Sonraki adım: Veriyi kaynağa göre güncelle\n";
echo "════════════════════════════════════════════════════════════\n";
