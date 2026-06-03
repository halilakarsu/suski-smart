<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Aboneler;
use Carbon\Carbon;

class SyncAbonelerFromExcel extends Command
{
    protected $signature = 'app:sync-aboneler-from-excel
                            {--file=/Users/akarsu/Desktop/aboneler.xls : Excel dosyası yolu}
                            {--dry-run : Değişiklikleri göster ama kaydetme}';

    protected $description = 'Excel aboneler listesini veritabanı ile senkronize et';

    public function handle()
    {
        $filePath = $this->option('file');
        $isDryRun = $this->option('dry-run');

        if (!file_exists($filePath)) {
            $this->error("Dosya bulunamadı: {$filePath}");
            return 1;
        }

        $this->info('Excel dosyası okunuyor...');

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Excel sütun haritası (0-indexed)
            $cols = [
                'sira' => 1,           // B
                'ilce' => 2,           // C
                'abone_no' => 3,       // D
                'pmum' => 4,           // E
                'sayac_no' => 5,       // F
                'og_durumu' => 6,      // G (0=AG, 1=OG)
                'adres' => 7,          // H
                'tarife' => 8,         // I (hesap_adi)
                'tesis_cinsi' => 9,    // J (baglanti_grubu)
                'dagitim' => 10,       // K
                'carpan' => 11,        // L
            ];

            $this->info("Excel yapısı tespit edildi:");
            $this->line("  - Abone Tesis No: Kolon D");
            $this->line("  - Sayaç No: Kolon F");
            $this->line("  - İlçe: Kolon C");
            $this->line("  - Adres: Kolon H");
            $this->line("  - Tarife (Hesap Adı): Kolon I");
            $this->line("  - Tesis Cinsi (Bağlantı Türü): Kolon J");
            $this->line("  - OG Durumu: Kolon G");

            // Excel verilerini oku (header satırı 2, data satırı 3+)
            $excelAbones = [];
            $rows = $sheet->toArray(null, true, true, false);
            
            foreach (array_slice($rows, 3) as $row) {
                $aboneNo = isset($row[$cols['abone_no']]) ? trim((string)$row[$cols['abone_no']]) : '';
                
                if (!empty($aboneNo) && is_numeric($aboneNo)) {
                    $excelAbones[$aboneNo] = [
                        'sayac_no' => trim((string)($row[$cols['sayac_no']] ?? '')),
                        'ilce' => trim((string)($row[$cols['ilce']] ?? '')),
                        'adres' => trim((string)($row[$cols['adres']] ?? '')),
                        'tarife' => trim((string)($row[$cols['tarife']] ?? '')),
                        'tesis_cinsi' => trim((string)($row[$cols['tesis_cinsi']] ?? '')),
                        'og_durumu' => (int)($row[$cols['og_durumu']] ?? 0),
                        'pmum' => trim((string)($row[$cols['pmum']] ?? '')),
                        'dagitim' => trim((string)($row[$cols['dagitim']] ?? '')),
                        'carpan' => (float)($row[$cols['carpan']] ?? 1),
                    ];
                }
            }

            $this->info("\nToplam " . count($excelAbones) . " abone Excel dosyasında bulundu");

            // Senkronizasyon istatistikleri
            $stats = [
                'eklendi' => 0,
                'guncellendi' => 0,
                'sayac_degisti' => 0,
                'tarife_degisti' => 0,
                'baglanti_degisti' => 0,
                'og_degisti' => 0,
                'adres_degisti' => 0,
            ];

            $this->line("\n=== SENKRONİZASYON BAŞLANIYOR ===\n");

            $now = Carbon::now();

            foreach ($excelAbones as $aboneNo => $excelData) {
                $dbAbone = Aboneler::where('ABONE_TESIS_NO', $aboneNo)->first();

                if (!$dbAbone) {
                    // YENİ ABONE EKLE
                    if (!$isDryRun) {
                        Aboneler::create([
                            'ABONE_TESIS_NO' => $aboneNo,
                            'PMUM' => $excelData['pmum'],
                            'SAYAC_SERI_NO' => $excelData['sayac_no'],
                            'BOLGE_ADI' => $excelData['ilce'],
                            'ADRES' => $excelData['adres'],
                            'hesap_adi' => $excelData['tarife'],
                            'baglanti_grubu' => $excelData['tesis_cinsi'],
                            'OG_durumu' => $excelData['og_durumu'],
                            'dagitim_merkezi' => $excelData['dagitim'],
                            'carpan' => $excelData['carpan'],
                            'is_active' => true,
                            'guncelleme_tarihi' => $now,
                            'created_via' => 'excel_sync',
                        ]);
                    }
                    $stats['eklendi']++;
                    $this->line("<info>✓ EKLENDI:</info> {$aboneNo} (Sayaç: {$excelData['sayac_no']})");
                } else {
                    // MEVCUT ABONEYİ GÜNCELLE
                    $degisiklikler = [];
                    $updateData = [];

                    // Sayaç No
                    if ($dbAbone->SAYAC_SERI_NO !== $excelData['sayac_no']) {
                        if (!empty($excelData['sayac_no'])) {
                            if (!$isDryRun) {
                                $dbAbone->updateSayacWithHistory($excelData['sayac_no'], $now);
                            }
                            $degisiklikler[] = "Sayaç: {$dbAbone->SAYAC_SERI_NO} → {$excelData['sayac_no']}";
                            $stats['sayac_degisti']++;
                        }
                    }

                    // Tarife (hesap_adi)
                    if ($dbAbone->hesap_adi !== $excelData['tarife']) {
                        $updateData['hesap_adi'] = $excelData['tarife'];
                        $degisiklikler[] = "Tarife: {$dbAbone->hesap_adi} → {$excelData['tarife']}";
                        $stats['tarife_degisti']++;
                    }

                    // Tesis Cinsi (baglanti_grubu)
                    if ($dbAbone->baglanti_grubu !== $excelData['tesis_cinsi']) {
                        $updateData['baglanti_grubu'] = $excelData['tesis_cinsi'];
                        $degisiklikler[] = "Bağlantı: {$dbAbone->baglanti_grubu} → {$excelData['tesis_cinsi']}";
                        $stats['baglanti_degisti']++;
                    }

                    // OG Durumu
                    if ($dbAbone->OG_durumu != $excelData['og_durumu']) {
                        $updateData['OG_durumu'] = $excelData['og_durumu'];
                        $eski = $dbAbone->OG_durumu ? 'OG' : 'AG';
                        $yeni = $excelData['og_durumu'] ? 'OG' : 'AG';
                        $degisiklikler[] = "OG: {$eski} → {$yeni}";
                        $stats['og_degisti']++;
                    }

                    // Adres
                    if ($dbAbone->ADRES !== $excelData['adres']) {
                        $updateData['ADRES'] = $excelData['adres'];
                        $degisiklikler[] = "Adres güncellendi";
                        $stats['adres_degisti']++;
                    }

                    // Diğer alanlar
                    if ($dbAbone->PMUM !== $excelData['pmum']) {
                        $updateData['PMUM'] = $excelData['pmum'];
                    }
                    if ($dbAbone->BOLGE_ADI !== $excelData['ilce']) {
                        $updateData['BOLGE_ADI'] = $excelData['ilce'];
                    }
                    if ($dbAbone->dagitim_merkezi !== $excelData['dagitim']) {
                        $updateData['dagitim_merkezi'] = $excelData['dagitim'];
                    }
                    if ($dbAbone->carpan !== $excelData['carpan']) {
                        $updateData['carpan'] = $excelData['carpan'];
                    }

                    // Güncellemeleri kaydet
                    if (!empty($updateData) || !empty($degisiklikler)) {
                        if (!empty($degisiklikler)) {
                            $this->line("<comment>⚠ GÜNCELLENDİ:</comment> {$aboneNo}");
                            foreach ($degisiklikler as $deg) {
                                $this->line("    - {$deg}");
                            }
                        }

                        if (!$isDryRun && !empty($updateData)) {
                            $dbAbone->updateWithHistory($updateData, $now);
                        }

                        $stats['guncellendi']++;
                    }
                }
            }

            // ÖZET
            $this->line("\n" . str_repeat("=", 60));
            $this->line("=== SENKRONIZASYON ÖZETI ===");
            $this->line(str_repeat("=", 60));
            
            $this->line("\n<info>Yeni Eklenen Aboneler:</info> " . $stats['eklendi']);
            $this->line("<info>Güncellenen Aboneler:</info> " . $stats['guncellendi']);
            
            if ($stats['guncellendi'] > 0) {
                $this->line("\n  Detaylı değişiklikler:");
                $this->line("    - Sayaç Değişimi: " . $stats['sayac_degisti']);
                $this->line("    - Tarife Güncelleme: " . $stats['tarife_degisti']);
                $this->line("    - Bağlantı Türü: " . $stats['baglanti_degisti']);
                $this->line("    - OG Durumu: " . $stats['og_degisti']);
                $this->line("    - Adres Güncelleme: " . $stats['adres_degisti']);
            }

            $totalExcel = count($excelAbones);
            $totalDb = Aboneler::count();
            $this->line("\n<info>Veritabanı Toplam:</info> {$totalDb}");
            $this->line("<info>Excel Toplam:</info> {$totalExcel}");

            if ($isDryRun) {
                $this->warn("\n⚠ DRY-RUN MODU: Değişiklikler KAYDEDILMEDI!");
            } else {
                $this->info("\n✓ Senkronizasyon tamamlandı!");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Hata: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
