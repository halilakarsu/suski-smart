<?php

namespace App\Console\Commands;

use App\Models\Aboneler;
use App\Models\Tesis;
use App\Models\TesisArac;
use App\Models\TesisArizaKaydi;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TesisImportCommand extends Command
{
    protected $signature = 'tesis:import {file? : Excel dosya yolu}';

    protected $description = 'Tesis Bilgi Sistemi Excel dosyasını veritabanına aktarır';

    private $aboneMap = [];

    public function handle()
    {
        ini_set('memory_limit', '4G');

        $file = $this->argument('file') ?: base_path('tesis-bilgi.xlsx');

        if (! file_exists($file)) {
            $this->error("Dosya bulunamadı: $file");

            return 1;
        }

        $this->info("Dosya okunuyor: $file");

        $reader = IOFactory::createReaderForFile($file);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);

        // Abone eşleştirme için map oluştur
        $this->loadAboneMap();

        $this->importArizaKayitlari($spreadsheet);
        $this->importTesisler($spreadsheet);
        $this->importAraclar($spreadsheet);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        gc_collect_cycles();

        $this->newLine();
        $this->info('İşlem tamamlandı.');

        return 0;
    }

    private function loadAboneMap(): void
    {
        $this->info('Abone eşleştirme tablosu yükleniyor...');
        $this->aboneMap = Aboneler::whereNotNull('ABONE_TESIS_NO')
            ->pluck('id', 'ABONE_TESIS_NO')
            ->toArray();
        $this->info(count($this->aboneMap).' abone yüklendi.');
    }

    private function findAboneId(?string $aboneNo): ?int
    {
        if (! $aboneNo || $aboneNo === '') {
            return null;
        }
        $trimmed = trim($aboneNo);

        return $this->aboneMap[$trimmed] ?? null;
    }

    private function importArizaKayitlari($spreadsheet): void
    {
        $ilceSheets = ['Akçakale', 'Harran', 'Suruç', 'Eyyübiye', 'Siverek', 'Haliliye', 'karaköprü', 'Hilvan', 'Bozova', 'Halfeti', 'Birecik', 'Viranşehir', 'Ceylanpınar'];

        $toplam = 0;
        $yeni = 0;

        foreach ($ilceSheets as $sheetName) {
            if (! $spreadsheet->sheetNameExists($sheetName)) {
                $this->warn("Sayfa bulunamadı: $sheetName");

                continue;
            }

            $sheet = $spreadsheet->getSheetByName($sheetName);
            $rows = $sheet->getRowIterator();
            $headerRow = null;
            $headerIdx = 0;

            foreach ($rows as $rowObj) {
                if ($rowObj->getRowIndex() > 10) {
                    break;
                }
                $cells = [];
                foreach ($rowObj->getCellIterator() as $cell) {
                    $cells[] = $cell->getValue();
                }
                $h0 = mb_strtolower(trim((string) ($cells[0] ?? '')));
                if ($h0 === 'sıra no' || $h0 === 'sira no') {
                    $headerRow = $cells;
                    $headerIdx = $rowObj->getRowIndex();
                    break;
                }
            }

            if (! $headerRow) {
                $this->warn("Başlık bulunamadı: $sheetName");

                continue;
            }

            $headers = array_map(function ($h) {
                return mb_strtolower(trim((string) $h));
            }, $headerRow);

            $colMap = $this->mapColumns($headers, [
                'sıra no' => 'sira_no',
                'sira no' => 'sira_no',
                'kuyu no' => 'kuyu_no',
                'tutanak no' => 'tutanak_no',
                'ekip' => 'ekip',
                'tarih' => 'tarih',
                'abone no' => 'abone_no',
                'sayaç no' => 'sayac_no',
                'sayac no' => 'sayac_no',
                'i̇lçe' => 'ilce',
                'ilçe' => 'ilce',
                'ilce' => 'ilce',
                'mahalle' => 'mahalle',
                'sokak' => 'sokak',
                'cbs_x' => 'cbs_x',
                'cbs_y' => 'cbs_y',
                'arıza türü' => 'ariza_turu',
                'ariza turu' => 'ariza_turu',
            ]);

            $rows->resetStart($headerIdx + 1);
            $eklenen = 0;

            foreach ($rows as $rowObj) {
                $row = [];
                $colIdx = 0;
                foreach ($rowObj->getCellIterator() as $cell) {
                    $row[$colIdx] = $cell->getValue();
                    $colIdx++;
                }

                $data = [];
                $hasData = false;
                foreach ($colMap as $field => $dbField) {
                    $val = $row[$field] ?? null;
                    if ($val !== null && $val !== '') {
                        $val = $this->normalizeValue($val);
                        $hasData = true;
                    }
                    $data[$dbField] = $val;
                }

                if (! $hasData) {
                    continue;
                }

                $data['tarih'] = $this->normalizeDate($data['tarih'] ?? null);
                $data['cbs_x'] = $this->normalizeDecimal($data['cbs_x'] ?? null);
                $data['cbs_y'] = $this->normalizeDecimal($data['cbs_y'] ?? null);
                $data['abone_id'] = $this->findAboneId($data['abone_no'] ?? null);

                TesisArizaKaydi::create($data);
                $eklenen++;
            }

            $this->info("  $sheetName: $eklenen arıza kaydı eklendi.");
            $yeni += $eklenen;
            $toplam += $eklenen;
        }

        $this->newLine();
        $this->line("Arıza kayıtları: $yeni yeni kayıt.");
    }

    private function importTesisler($spreadsheet): void
    {
        $sheets = [
            ['name' => 'tarımsal sulama', 'durum' => 'aktif'],
            ['name' => 'pasif tesisler', 'durum' => 'pasif'],
        ];

        $yeni = 0;

        foreach ($sheets as $s) {
            $sheetName = $s['name'];
            $durum = $s['durum'];

            if (! $spreadsheet->sheetNameExists($sheetName)) {
                $this->warn("Sayfa bulunamadı: $sheetName");

                continue;
            }

            $sheet = $spreadsheet->getSheetByName($sheetName);
            $rows = $sheet->getRowIterator();
            $headerRow = null;
            $headerIdx = 0;

            foreach ($rows as $rowObj) {
                if ($rowObj->getRowIndex() > 5) {
                    break;
                }
                $cells = [];
                foreach ($rowObj->getCellIterator() as $cell) {
                    $cells[] = $cell->getValue();
                }
                $h0 = mb_strtolower(trim((string) ($cells[0] ?? '')));
                if ($h0 === 'sıra no' || $h0 === 'sira no') {
                    $headerRow = $cells;
                    $headerIdx = $rowObj->getRowIndex();
                    break;
                }
            }

            if (! $headerRow) {
                $this->warn("Başlık bulunamadı: $sheetName");

                continue;
            }

            $headers = array_map(function ($h) {
                return mb_strtolower(trim((string) $h));
            }, $headerRow);

            $colMap = $this->mapColumns($headers, [
                'sıra no' => 'sira_no',
                'sira no' => 'sira_no',
                'ilçe' => 'ilce',
                'ilce' => 'ilce',
                'mahalle' => 'mahalle',
                'sokak' => 'sokak',
                'kuyu no' => 'kuyu_no',
                'cbs_x' => 'cbs_x',
                'cbs_y' => 'cbs_y',
                'tesis kurulma tarihi' => 'tesis_kurulma_tarihi',
                'hibe tarihi' => 'hibe_tarihi',
                'abone tipi' => 'abone_tipi',
                'abone tarihi' => 'abone_tarihi',
                'sayaç no' => 'sayac_no',
                'sayac no' => 'sayac_no',
                'abone no' => 'abone_no',
                'abone iptali yazıldı mı?' => 'abone_iptali_yazildi_mi',
                'abone iptali yazildi mi?' => 'abone_iptali_yazildi_mi',
                'abone iptal edildi mi?' => 'abone_iptal_edildi_mi',
                'abone iptal edildi mi' => 'abone_iptal_edildi_mi',
                'kaçak elektrik kullanımı var mı?' => 'kacak_elektrik_kullanimi_var_mi',
                'kacak elektrik kullanimi var mi?' => 'kacak_elektrik_kullanimi_var_mi',
                'kaçak borcu var mı?' => 'kacak_borcu_var_mi',
                'kacak borcu var mi?' => 'kacak_borcu_var_mi',
                'toplam fatura tutarı' => 'toplam_fatura_tutari',
                'toplam fatura tutari' => 'toplam_fatura_tutari',
                'trafo gücü' => 'trafo_gucu',
                'trafo gucu' => 'trafo_gucu',
                'trafo seri no' => 'trafo_seri_no',
                'trafo cbs' => 'trafo_cbs',
                'enh durumu' => 'enh_durumu',
                'enh durumu' => 'enh_durumu',
                'keşif durumu' => 'kesif_durumu',
                'kesif durumu' => 'kesif_durumu',
                'demontaj tarihi' => 'demontaj_tarihi',
                'demontaj yapılan malzemeler' => 'demontaj_yapilan_malzemeler',
                'demontaj yapilan malzemeler' => 'demontaj_yapilan_malzemeler',
                'gelir' => 'gelir',
                'gider' => 'gider',
            ]);

            $rows->resetStart($headerIdx + 1);
            $eklenen = 0;

            foreach ($rows as $rowObj) {
                $row = [];
                $colIdx = 0;
                foreach ($rowObj->getCellIterator() as $cell) {
                    $row[$colIdx] = $cell->getValue();
                    $colIdx++;
                }

                $data = [];
                $hasData = false;
                foreach ($colMap as $field => $dbField) {
                    $val = $row[$field] ?? null;
                    if ($val !== null && $val !== '') {
                        $val = $this->normalizeValue($val);
                        $hasData = true;
                    }
                    $data[$dbField] = $val;
                }

                if (! $hasData) {
                    continue;
                }

                $data['durum'] = $durum;
                $data['abone_id'] = $this->findAboneId($data['abone_no'] ?? null);

                $data['cbs_x'] = $this->normalizeDecimal($data['cbs_x'] ?? null);
                $data['cbs_y'] = $this->normalizeDecimal($data['cbs_y'] ?? null);
                $data['toplam_fatura_tutari'] = $this->normalizeDecimal($data['toplam_fatura_tutari'] ?? null);
                $data['gelir'] = $this->normalizeDecimal($data['gelir'] ?? null);
                $data['gider'] = $this->normalizeDecimal($data['gider'] ?? null);
                foreach (['tesis_kurulma_tarihi', 'hibe_tarihi', 'abone_tarihi', 'demontaj_tarihi'] as $dateField) {
                    $data[$dateField] = $this->normalizeDate($data[$dateField] ?? null);
                }

                Tesis::create($data);
                $eklenen++;
            }

            $this->info("  $sheetName: $eklenen tesis eklendi (durum: $durum).");
            $yeni += $eklenen;
        }

        $this->newLine();
        $this->line("Tesisler: $yeni yeni kayıt.");
    }

    private function importAraclar($spreadsheet): void
    {
        if (! $spreadsheet->sheetNameExists('Araç Liste')) {
            $this->warn('Sayfa bulunamadı: Araç Liste');

            return;
        }

        $sheet = $spreadsheet->getSheetByName('Araç Liste');
        $rows = $sheet->getRowIterator();
        $headerRow = null;
        $headerIdx = 0;

        foreach ($rows as $rowObj) {
            if ($rowObj->getRowIndex() > 10) {
                break;
            }
            $cells = [];
            foreach ($rowObj->getCellIterator() as $cell) {
                $cells[] = $cell->getValue();
            }
            $h0 = mb_strtolower(trim((string) ($cells[0] ?? '')));
            if ($h0 === 's.n.' || $h0 === 's.n') {
                $headerRow = $cells;
                $headerIdx = $rowObj->getRowIndex();
                break;
            }
        }

        if (! $headerRow) {
            $this->warn('Başlık bulunamadı: Araç Liste');

            return;
        }

        $headers = array_map(function ($h) {
            return mb_strtolower(trim((string) $h));
        }, $headerRow);

        $colMap = $this->mapColumns($headers, [
            's.n.' => 'sira_no',
            'plaka' => 'plaka',
            'aracın cinsi' => 'aracin_cinsi',
            'aracin cinsi' => 'aracin_cinsi',
            'araç tipi' => 'arac_tipi',
            'arac tipi' => 'arac_tipi',
            'kullanıcı personel veya personeller' => 'kullanici_personel',
            'kullanici personel veya personeller' => 'kullanici_personel',
            'irtibat' => 'irtibat',
            'kullanıldığı iş' => 'kullanildigi_is',
            'kullanildigi is' => 'kullanildigi_is',
        ]);

        $rows->resetStart($headerIdx + 1);
        $eklenen = 0;

        foreach ($rows as $rowObj) {
            $row = [];
            $colIdx = 0;
            foreach ($rowObj->getCellIterator() as $cell) {
                $row[$colIdx] = $cell->getValue();
                $colIdx++;
            }

            $data = [];
            $hasData = false;
            foreach ($colMap as $field => $dbField) {
                $val = $row[$field] ?? null;
                if ($val !== null && $val !== '') {
                    $val = $this->normalizeValue($val);
                    $hasData = true;
                }
                $data[$dbField] = $val;
            }

            if (! $hasData) {
                continue;
            }

            TesisArac::create($data);
            $eklenen++;
        }

        $this->newLine();
        $this->line("Araçlar: $eklenen yeni kayıt.");
    }

    private function mapColumns(array $headers, array $mapping): array
    {
        $colMap = [];
        foreach ($headers as $idx => $header) {
            $header = trim((string) $header);
            if ($header === '') {
                continue;
            }
            $raw = mb_strtolower($header);
            $normalized = str_replace(
                ['ı', 'ü', 'ö', 'ç', 'ş', 'ğ', 'i̇', 'İ', 'Ü', 'Ö', 'Ç', 'Ş', 'Ğ'],
                ['i', 'u', 'o', 'c', 's', 'g', 'i', 'i', 'u', 'o', 'c', 's', 'g'],
                $raw
            );
            $normalized = preg_replace('/[^a-z0-9_ ]/', '', $normalized);
            if (isset($mapping[$raw])) {
                $colMap[$idx] = $mapping[$raw];
            } elseif (isset($mapping[$normalized])) {
                $colMap[$idx] = $mapping[$normalized];
            }
        }

        return $colMap;
    }

    private function normalizeValue($val)
    {
        if ($val === null) {
            return null;
        }
        if ($val instanceof \DateTimeInterface) {
            return $val;
        }
        if (is_numeric($val)) {
            return $val;
        }
        $trimmed = trim($val);

        return $trimmed === '' ? null : $trimmed;
    }

    private function normalizeDate($val)
    {
        if ($val instanceof \DateTimeInterface) {
            return $val->format('Y-m-d');
        }
        if (is_numeric($val) && $val > 40000) {
            $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);

            return $dt->format('Y-m-d');
        }

        return null;
    }

    private function normalizeDecimal($val)
    {
        if ($val === null || $val === '') {
            return null;
        }
        if (! is_numeric($val)) {
            return null;
        }
        $val = (float) $val;
        if (abs($val) > 99999999) {
            return null;
        }

        return $val;
    }
}
