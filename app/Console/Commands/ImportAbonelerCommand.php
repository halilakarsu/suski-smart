<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aboneler;
use App\Models\Bolgeler;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportAbonelerCommand extends Command
{
    protected $signature = 'suski:import-aboneler';
    protected $description = 'Örnek faturalar klasöründeki Excel dosyalarından aboneleri tekrarsız alır. Bölge kodu/adı bolgeler tablosundan eşleştirilir.';

    public function handle()
    {
        ini_set('memory_limit', '4G');

        $dir = public_path('ornek faturalar');
        if (!is_dir($dir)) {
            $this->error("Klasör bulunamadı: $dir");
            return 1;
        }

        $files = glob($dir . '/*.xlsx');
        if (empty($files)) {
            $this->error("XLSX dosyası bulunamadı.");
            return 1;
        }

        // Bölgeler tablosunu belleğe al (kod => adi eşlemesi)
        $bolgelerMap = Bolgeler::all()->keyBy('bolge_kodu');
        $this->info("Bölgeler tablosundan {$bolgelerMap->count()} bölge yüklendi.");

        $yeni       = 0;
        $guncellenen = 0;
        $atlanan    = 0;

        foreach ($files as $file) {
            $this->info("Okunuyor: " . basename($file));
            $reader = IOFactory::createReaderForFile($file);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getActiveSheet();

            $headerRow = [];
            $headerIdx = 0;
            $rowsIterator = $sheet->getRowIterator();

            // Başlık satırını bul
            foreach ($rowsIterator as $rowObj) {
                $ro = $rowObj->getRowIndex();
                if ($ro > 20) break;

                $cells = [];
                foreach ($rowObj->getCellIterator() as $cell) {
                    $cells[] = $cell->getValue();
                }

                $clean = array_map(function ($h) {
                    return preg_replace('/[^a-z0-9_]/u', '', mb_strtolower(trim((string)$h), 'UTF-8'));
                }, $cells);

                $hit = 0;
                foreach ($clean as $h) {
                    if (str_contains($h, 'fatura') || str_contains($h, 'tesisat') || str_contains($h, 'okuma')) $hit++;
                }
                if ($hit >= 2) {
                    $headerRow = $cells;
                    $headerIdx = $ro;
                    break;
                }
            }

            if (empty($headerRow)) {
                $this->warn("Başlık bulunamadı: " . basename($file));
                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
                gc_collect_cycles();
                continue;
            }

            $headers = array_map(function ($h) {
                return preg_replace('/[^a-z0-9_]/u', '', mb_strtolower(trim((string)$h), 'UTF-8'));
            }, $headerRow);

            $rowsIterator->resetStart((int)$headerIdx + 1);

            $bar = null;
            foreach ($rowsIterator as $rowObj) {
                $payload = [];
                $colIdx  = 0;
                foreach ($rowObj->getCellIterator() as $cell) {
                    $key = $headers[$colIdx] ?? '';
                    if ($key) {
                        $val           = trim((string)$cell->getValue());
                        $payload[$key] = $val;
                        $payload[strtolower(trim((string)$headerRow[$colIdx]))] = $val;
                    }
                    $colIdx++;
                }

                $tesisat = $this->findField($payload, ['tesisat', 'tesisatno', 'tesisat_no', 'tesisat no']);
                if (!$tesisat) {
                    $atlanan++;
                    continue;
                }

                // ─────── Bölge Kodu Eşleştirmesi ───────
                $rawBolgeKodu = $this->findField($payload, [
                    'blg', 'f_bolge_kodu', 'ilce_kodu', 'ilce kodu', 'bld', 'bolge_kodu', 'bolge kodu'
                ]);
                $rawBolgeAdi = $this->findField($payload, [
                    'bolgeadi', 'bolge_adi', 'bolge adi', 'dagitim', 'ilce', 'altisletmeadi', 'alt isletme adi'
                ]);

                // Bölge kodunu bolgeler tablosunda ara
                $bolgeKodu = null;
                $bolgeAdi  = $rawBolgeAdi;

                if ($rawBolgeKodu && $bolgelerMap->has($rawBolgeKodu)) {
                    $bolge     = $bolgelerMap->get($rawBolgeKodu);
                    $bolgeKodu = $bolge->bolge_kodu;
                    $bolgeAdi  = $bolge->bolge_adi;
                } elseif ($rawBolgeAdi) {
                    // Kodu yoksa bolge_adi üzerinden eşleştir
                    $eslesen = $bolgelerMap->first(function ($b) use ($rawBolgeAdi) {
                        return mb_strtolower(trim($b->bolge_adi)) === mb_strtolower(trim($rawBolgeAdi));
                    });
                    if ($eslesen) {
                        $bolgeKodu = $eslesen->bolge_kodu;
                        $bolgeAdi  = $eslesen->bolge_adi;
                    }
                }
                // ──────────────────────────────────────

                $yeniAboneData = [
                    'BOLGE_ADI'           => $bolgeAdi,
                    'BOLGE_KODU'          => $bolgeKodu,
                    'ADRES'               => $this->findField($payload, ['adres', 'adress', 'a d r e s']),
                    'SAYAC_SERI_NO'       => $this->findField($payload, ['sayacno', 'sayac_no', 'sayacserino', 'sayac_seri_no', 'sayac seri no']),
                    'created_via'         => 'import',
                    'is_new'              => true,
                    'is_updated'          => false,
                ];

                $mevcutAbone = Aboneler::where('ABONE_TESIS_NO', $tesisat)->first();

                if (!$mevcutAbone) {
                    Aboneler::create(array_merge(['ABONE_TESIS_NO' => $tesisat], $yeniAboneData));
                    $yeni++;
                } else {
                    $degisiklikVar = false;
                    foreach (['ADRES', 'SAYAC_SERI_NO'] as $alan) {
                        if ($yeniAboneData[$alan] && (string)$mevcutAbone->$alan !== (string)$yeniAboneData[$alan]) {
                            $degisiklikVar = true;
                            break;
                        }
                    }
                    if ($degisiklikVar) {
                        $mevcutAbone->update([
                            'prev_adres'          => $mevcutAbone->ADRES,
                            'prev_sayac_seri_no'  => $mevcutAbone->SAYAC_SERI_NO,
                            'ADRES'               => $yeniAboneData['ADRES'],
                            'SAYAC_SERI_NO'       => $yeniAboneData['SAYAC_SERI_NO'],
                            'is_updated'          => true,
                        ]);
                        $guncellenen++;
                    }
                }
            }

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            gc_collect_cycles();
        }

        $this->newLine();
        $this->info("─────────────────────────────────────");
        $this->info("İşlem Tamamlandı!");
        $this->info("  Eklenen Yeni Abone : " . number_format($yeni));
        $this->info("  Güncellenen Abone  : " . number_format($guncellenen));
        $this->info("  Atlanan (Tesisat Yok): " . number_format($atlanan));
        $this->info("─────────────────────────────────────");

        return 0;
    }

    private function findField(array $row, array $possibleKeys): ?string
    {
        foreach ($possibleKeys as $key) {
            $keyTemiz = preg_replace('/[^a-z0-9_]/u', '', strtolower($key));
            if (isset($row[$keyTemiz]) && $row[$keyTemiz] !== '') {
                return $row[$keyTemiz];
            }
            // Orijinal ad ile de dene
            if (isset($row[$key]) && $row[$key] !== '') {
                return $row[$key];
            }
        }
        return null;
    }
}
