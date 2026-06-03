<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aboneler;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SyncAbonelerComprehensive extends Command
{
    protected $signature = 'app:sync-aboneler-comprehensive {--dry-run}';
    protected $description = 'Veritabanını Excel verisi ile senkronize et - kapsamlı güncellemeler';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('');
        $this->info('════════════════════════════════════════════════════════');
        $this->info('  ABONE SENKRONİZASYONU - KAPSAMLı GÜNCELLEME');
        $this->info('════════════════════════════════════════════════════════');
        $this->info('');
        
        if ($isDryRun) {
            $this->line('⚠️  DRY-RUN MODU: Değişiklikler kaydedilmeyecek');
            $this->info('');
        }

        // Excel dosyasını oku
        $excelFile = base_path('aboneler.xls');
        if (!file_exists($excelFile)) {
            $this->error("Excel dosyası bulunamadı: $excelFile");
            return 1;
        }

        try {
            $spreadsheet = IOFactory::load($excelFile);
            $sheet = $spreadsheet->getActiveSheet();
        } catch (\Exception $e) {
            $this->error("Excel okunamadı: " . $e->getMessage());
            return 1;
        }

        // Excel verilerini belleğe yükle
        $excelData = [];
        $row = 4; // Veriler satır 4'ten başlıyor
        while ($row <= $sheet->getHighestRow()) {
            $aboneTesisNo = trim((string)$sheet->getCell('D' . $row)->getValue());
            
            if (empty($aboneTesisNo)) {
                $row++;
                continue;
            }

            $excelData[$aboneTesisNo] = [
                'ABONE_TESIS_NO' => $aboneTesisNo,
                'PMUM' => $sheet->getCell('E' . $row)->getValue(),
                'SAYAC_SERI_NO' => trim((string)$sheet->getCell('F' . $row)->getValue()),
                'SOZLESME_GUCU' => $sheet->getCell('G' . $row)->getValue(),
                'ADRES' => trim((string)$sheet->getCell('H' . $row)->getValue()),
                'tarife' => trim((string)$sheet->getCell('I' . $row)->getValue()),
                'tesis_cinsi' => trim((string)$sheet->getCell('J' . $row)->getValue()),
                'dagitim_merkezi' => trim((string)$sheet->getCell('K' . $row)->getValue()),
                'carpan' => $sheet->getCell('L' . $row)->getValue(),
                'BOLGE_ADI' => trim((string)$sheet->getCell('C' . $row)->getValue()),
                'sira' => $sheet->getCell('B' . $row)->getValue(),
                'EXCEL_DATE' => now()->toDateTimeString(), // Excel'den okunan tarih
            ];

            $row++;
        }

        $this->line("Excel'den okunan toplam abone: " . count($excelData));
        $this->info('');

        // İstatistikler
        $eklenecek = 0;
        $guncellenecek = 0;
        $degisim = [
            'sayac' => 0,
            'adres' => 0,
            'tarife' => 0,
            'tesis_cinsi' => 0,
        ];

        // 1. ABONE EKLEMELERİ
        $this->line('1️⃣  YENİ ABONELERİ KONTROL ET');
        $this->line('─────────────────────────────────────────────────────────');

        foreach ($excelData as $tesisNo => $data) {
            $abone = Aboneler::where('ABONE_TESIS_NO', $tesisNo)->first();

            if (!$abone) {
                // Yeni abone
                $eklenecek++;
                $this->line("   ➕ Yeni abone: $tesisNo | Sayaç: {$data['SAYAC_SERI_NO']}");

                if (!$isDryRun) {
                    Aboneler::create([
                        'ABONE_TESIS_NO' => $tesisNo,
                        'PMUM' => $data['PMUM'],
                        'SAYAC_SERI_NO' => $data['SAYAC_SERI_NO'],
                        'ADRES' => $data['ADRES'],
                        'BOLGE_ADI' => $data['BOLGE_ADI'],
                        'tarife' => $data['tarife'],
                        'tesis_cinsi' => $data['tesis_cinsi'],
                        'dagitim_merkezi' => $data['dagitim_merkezi'],
                        'carpan' => $data['carpan'],
                        'OG_durumu' => 0, // Varsayılan olarak AG
                        'baglanti_grubu' => 'AG',
                        'is_active' => true,
                        'is_new' => true,
                        'created_via' => 'excel_sync',
                        'guncelleme_tarihi' => now(),
                    ]);
                }
            }
        }

        $this->info('');
        $this->line("   Toplam yeni abone: $eklenecek");
        $this->info('');

        // 2. MEVCUT ABONELERİ GÜNCELLE
        $this->line('2️⃣  MEVCUT ABONELERİ GÜNCELLE');
        $this->line('─────────────────────────────────────────────────────────');

        $dbAboneler = Aboneler::all()->keyBy('ABONE_TESIS_NO');

        foreach ($excelData as $tesisNo => $data) {
            if (!isset($dbAboneler[$tesisNo])) {
                continue; // Zaten ekledik, geç
            }

            $abone = $dbAboneler[$tesisNo];
            $bilgiDegisti = false;

            // Güncellenecek alanlar
            $updateData = [];

            // Sayaç kontrolü
            if ($abone->SAYAC_SERI_NO !== $data['SAYAC_SERI_NO']) {
                $this->line("   🔄 Sayaç değişim: {$tesisNo} | Eski: {$abone->SAYAC_SERI_NO} → Yeni: {$data['SAYAC_SERI_NO']}");
                $updateData['SAYAC_SERI_NO'] = $data['SAYAC_SERI_NO'];
                $updateData['prev_sayac_seri_no'] = $abone->SAYAC_SERI_NO;
                $degisim['sayac']++;
                $bilgiDegisti = true;
            }

            // Adres kontrolü
            if ($abone->ADRES !== $data['ADRES']) {
                $this->line("   📍 Adres güncelleme: {$tesisNo}");
                $updateData['ADRES'] = $data['ADRES'];
                $updateData['prev_adres'] = $abone->ADRES;
                $degisim['adres']++;
                $bilgiDegisti = true;
            }

            // Tarife kontrolü
            if (($abone->tarife ?? '') !== ($data['tarife'] ?? '')) {
                $this->line("   📋 Tarife: {$tesisNo} | {$data['tarife']}");
                $updateData['tarife'] = $data['tarife'];
                $updateData['prev_tarife'] = $abone->tarife;
                $degisim['tarife']++;
                $bilgiDegisti = true;
            }

            // Tesis cinsi kontrolü
            if (($abone->tesis_cinsi ?? '') !== ($data['tesis_cinsi'] ?? '')) {
                $this->line("   🏢 Tesis cinsi: {$tesisNo} | {$data['tesis_cinsi']}");
                $updateData['tesis_cinsi'] = $data['tesis_cinsi'];
                $updateData['prev_tesis_cinsi'] = $abone->tesis_cinsi;
                $degisim['tesis_cinsi']++;
                $bilgiDegisti = true;
            }

            // Diğer alanlar
            if (($abone->dagitim_merkezi ?? '') !== ($data['dagitim_merkezi'] ?? '')) {
                $updateData['dagitim_merkezi'] = $data['dagitim_merkezi'];
            }

            if ($abone->carpan != $data['carpan']) {
                $updateData['carpan'] = $data['carpan'];
            }

            if ($abone->BOLGE_ADI !== $data['BOLGE_ADI']) {
                $updateData['BOLGE_ADI'] = $data['BOLGE_ADI'];
            }

            // Eksik hesap_adi ve baglanti_grubu
            if (is_null($abone->hesap_adi)) {
                // Eğer tarife var ise hesap_adi set et
                if (!empty($data['tarife'])) {
                    $updateData['hesap_adi'] = $abone->ABONE_TESIS_NO . ' - ' . $data['tarife'];
                }
            }

            if (is_null($abone->baglanti_grubu)) {
                // OG_durumu'na göre baglanti_grubu belirle
                $updateData['baglanti_grubu'] = $abone->OG_durumu ? 'OG' : 'AG';
            }

            if (!empty($updateData)) {
                $updateData['is_updated'] = true;
                $updateData['guncelleme_tarihi'] = now();

                if (!$isDryRun) {
                    $abone->update($updateData);
                    $guncellenecek++;
                } else {
                    $guncellenecek++;
                }
            }
        }

        $this->info('');
        $this->line("   Sayaç değişimleri: {$degisim['sayac']}");
        $this->line("   Adres güncellemeleri: {$degisim['adres']}");
        $this->line("   Tarife güncellemeleri: {$degisim['tarife']}");
        $this->line("   Tesis cinsi güncellemeleri: {$degisim['tesis_cinsi']}");
        $this->line("   Toplam güncellenen abone: $guncellenecek");
        $this->info('');

        // ÖZETİ
        $this->info('════════════════════════════════════════════════════════');
        $this->line("✓ İşlem tamamlandı!");
        $this->line("  • Eklenecek yeni abone: $eklenecek");
        $this->line("  • Güncellenecek abone: $guncellenecek");
        
        if ($isDryRun) {
            $this->line("  ⚠️  DRY-RUN: Değişiklikler kaydedilmedi!");
        }
        
        $this->info('════════════════════════════════════════════════════════');

        return 0;
    }
}
