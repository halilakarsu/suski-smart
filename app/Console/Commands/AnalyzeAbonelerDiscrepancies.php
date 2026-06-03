<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aboneler;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AnalyzeAbonelerDiscrepancies extends Command
{
    protected $signature = 'app:analyze-discrepancies';
    protected $description = 'Veritabanı, Excel ve faturalar arasındaki tutarsızlıkları analiz et';

    public function handle()
    {
        $this->info('');
        $this->info('════════════════════════════════════════════════════════════');
        $this->info('  TUTARSIZLIK ANALİZİ: Veritabanı vs Excel vs Faturalar');
        $this->info('════════════════════════════════════════════════════════════');
        $this->info('');

        // ============ 1. VERİTABANINDAN ABONE LİSTESİ ============
        $this->line('1️⃣  VERİTABANINDAN ABONELER');
        $this->line('─────────────────────────────────────────────────────────');

        $dbAboneler = Aboneler::select(
            'ABONE_TESIS_NO',
            'PMUM',
            'SAYAC_SERI_NO',
            'hesap_adi',
            'abone_grubu',
            'tarife',
            'OG_durumu',
            'baglanti_grubu',
            'tesis_cinsi',
            'BOLGE_ADI',
            'ADRES',
            'guncelleme_tarihi'
        )->get()->keyBy('ABONE_TESIS_NO');

        $this->line("   ✓ Toplam abone: " . $dbAboneler->count());
        $this->line("   • Eksik hesap_adi: " . $dbAboneler->whereNull('hesap_adi')->count());
        $this->line("   • Eksik abone_grubu: " . $dbAboneler->whereNull('abone_grubu')->count());
        $this->line("   • Eksik tarife: " . $dbAboneler->whereNull('tarife')->count());
        $this->line("   • Eksik OG_durumu: " . $dbAboneler->whereNull('OG_durumu')->count());
        $this->line("   • Eksik baglanti_grubu: " . $dbAboneler->whereNull('baglanti_grubu')->count());
        $this->info('');

        // ============ 2. EXCEL'DEN ABONE LİSTESİ ============
        $this->line('2️⃣  EXCEL\'DEN ABONELER');
        $this->line('─────────────────────────────────────────────────────────');

        $excelFile = '/var/www/html/aboneler.xls';
        if (!file_exists($excelFile)) {
            // Fallback: Proje dizininde ara
            $excelFile = base_path('aboneler.xls');
        }

        try {
            $spreadsheet = IOFactory::load($excelFile);
            $sheet = $spreadsheet->getActiveSheet();
            $excelAboneler = [];

            // A5'ten başlayarak oku (header'ı geç)
            $row = 5;
            while ($row <= $sheet->getHighestRow()) {
                $aboneTesisNo = trim((string) $sheet->getCell('D' . $row)->getValue());

                if (empty($aboneTesisNo)) {
                    $row++;
                    continue;
                }

                $excelAboneler[$aboneTesisNo] = [
                    'ABONE_TESIS_NO' => $aboneTesisNo,
                    'SIRA' => $sheet->getCell('B' . $row)->getValue(),
                    'ILCESI' => trim((string) $sheet->getCell('C' . $row)->getValue()),
                    'PMUM' => $sheet->getCell('E' . $row)->getValue(),
                    'SAYAC_NO' => trim((string) $sheet->getCell('F' . $row)->getValue()),
                    'SOZLESME_GUCU' => $sheet->getCell('G' . $row)->getValue(),
                    'ADRES' => trim((string) $sheet->getCell('H' . $row)->getValue()),
                ];

                $row++;
            }

            $this->line("   ✓ Toplam abone: " . count($excelAboneler));
            $this->info('');
        } catch (\Exception $e) {
            $this->error("   ✗ Excel okunamadı: " . $e->getMessage());
            return 1;
        }

        // ============ 3. FATURALARDAN ABONE BİLGİLERİ ============
        $this->line('3️⃣  FATURALARIN ABONE BİLGİLERİ');
        $this->line('─────────────────────────────────────────────────────────');

        $faturaAboneler = [];
        $faturaDir = '/Users/akarsu/Desktop/suski/public/ornek faturalar';

        if (is_dir($faturaDir)) {
            $files = glob($faturaDir . '/*.xlsx');
            $this->line("   Tarama dosyaları: " . count($files));

            foreach ($files as $file) {
                try {
                    $spreadsheet = IOFactory::load($file);
                    $sheet = $spreadsheet->getActiveSheet();

                    // Faturalardaki sayaç ve abone no bilgisini bul
                    $aboneTesisNo = null;
                    $sayacNo = null;

                    // Satır satır tara
                    for ($row = 1; $row <= min(50, $sheet->getHighestRow()); $row++) {
                        $cellValue = (string) $sheet->getCell('A' . $row)->getValue();

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

        $this->line("   ✓ Faturada bulunan abone: " . count($faturaAboneler));
        $this->info('');

        // ============ 4. KARŞILAŞTIRMA ============
        $this->line('4️⃣  KARŞILAŞTIRMA VE TUTARSIZLIKLAR');
        $this->line('─────────────────────────────────────────────────────────');

        // Collection'ları array'e çevir
        $dbAbonelerArray = $dbAboneler->toArray();

        // Excel'de olup DB'de olmayan
        $excelDeBulunanDbDeBulunmayan = array_diff_key($excelAboneler, $dbAbonelerArray);
        $this->line("   • Excel'de olup VT'de olmayan: " . count($excelDeBulunanDbDeBulunmayan) . " abone");

        // DB'de olup Excel'de olmayan
        $dbDeBulunanExcelDeBulunmayan = array_diff_key($dbAbonelerArray, $excelAboneler);
        $this->line("   • VT'de olup Excel'de olmayan: " . count($dbDeBulunanExcelDeBulunmayan) . " abone");

        // Her iki yerde de olan (karşılaştırma)
        $ortakAboneler = array_intersect_key($dbAbonelerArray, $excelAboneler);
        $this->line("   • Her iki kaynakta da olan: " . count($ortakAboneler) . " abone");

        // Faturalardaki aboneler
        $faturaVeDbDeFarklı = [];
        foreach ($faturaAboneler as $no => $fatura) {
            if (!isset($dbAboneler[$no])) {
                $faturaVeDbDeFarklı[$no] = $fatura;
            }
        }
        $this->line("   • Faturada olup VT'de olmayan: " . count($faturaVeDbDeFarklı) . " abone");
        $this->info('');

        // ============ 5. ÖRNEK VERİLER ============
        $this->line('5️⃣  ÖRNEK VERİLER');
        $this->line('─────────────────────────────────────────────────────────');

        $this->line('');
        $this->line("   Excel'de olup VT'de OLMAYAN (ilk 5):");
        $count = 0;
        foreach ($excelDeBulunanDbDeBulunmayan as $no => $abone) {
            if ($count++ >= 5)
                break;
            $this->line("      • $no | Sayaç: {$abone['SAYAC_NO']} | Adres: {$abone['ADRES']}");
        }

        $this->line('');
        $this->line("   VT'de olup Excel'de OLMAYAN (ilk 5):");
        $count = 0;
        foreach ($dbDeBulunanExcelDeBulunmayan as $no => $abone) {
            if ($count++ >= 5)
                break;
            $this->line("      • $no | Sayaç: {$abone['SAYAC_SERI_NO']} | Adres: {$abone['ADRES']}");
        }

        $this->line('');
        $this->line("   Eksik ALAN örneği (hesap_adi=null) (ilk 5):");
        $count = 0;
        foreach ($dbAboneler as $abone) {
            if ($count++ >= 5)
                break;
            if (is_null($abone['hesap_adi'])) {
                $this->line("      • {$abone['ABONE_TESIS_NO']} | hesap_adi: NULL | tesis_cinsi: {$abone['tesis_cinsi']}");
            }
        }

        $this->info('');
        $this->info('════════════════════════════════════════════════════════════');
        $this->info('  ✓ Analiz tamamlandı.');
        $this->info('════════════════════════════════════════════════════════════');

        return 0;
    }
}
