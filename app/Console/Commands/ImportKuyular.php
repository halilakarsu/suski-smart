<?php

namespace App\Console\Commands;

use App\Models\Kuyu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportKuyular extends Command
{
    protected $signature   = 'kuyu:import {file? : CSV dosyasının yolu (varsayılan: storage/app/kuyular_import.csv)}';
    protected $description = 'kuyular CSV dosyasından veritabanına veri aktarır';

    public function handle(): int
    {
        $file = $this->argument('file') ?? storage_path('app/kuyular_import.csv');

        if (!file_exists($file)) {
            $this->error("Dosya bulunamadı: $file");
            return self::FAILURE;
        }

        $this->info("Okunuyor: $file");

        $handle = fopen($file, 'r');
        if (!$handle) {
            $this->error("Dosya açılamadı!");
            return self::FAILURE;
        }

        // Başlık satırını oku ve atla
        fgetcsv($handle);

        // Mevcut verileri temizle
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Kuyu::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->info('Mevcut kayıtlar temizlendi.');

        $batch    = [];
        $inserted = 0;
        $skipped  = 0;
        $now      = now()->toDateTimeString();

        while (($row = fgetcsv($handle)) !== false) {
            // Tamamen boş satırı atla
            if (count(array_filter($row, fn($v) => trim($v) !== '')) === 0) {
                $skipped++;
                continue;
            }

            $batch[] = [
                'kuyu_no'             => $this->cleanStr($row[0] ?? null, 50),
                'ilce'                => $this->cleanStr($row[1] ?? null, 100),
                'adres'               => $this->cleanStr($row[2] ?? null, 500),
                'demontaj_derinligi'  => $this->cleanDecimal($row[3] ?? null),
                'montaj_derinligi'    => $this->cleanDecimal($row[4] ?? null),
                'depo_bilgisi'        => $this->cleanStr($row[5] ?? null, 300),
                'boru_tipi'           => $this->cleanStr($row[6] ?? null, 200),
                'kablo'               => $this->cleanStr($row[7] ?? null, 200),
                'motor'               => $this->cleanStr($row[8] ?? null, 300),
                'pompa'               => $this->cleanStr($row[9] ?? null, 300),
                'debi'                => $this->cleanStr($row[10] ?? null, 100),
                'aciklama'            => $this->cleanStr($row[11] ?? null),
                'durum'               => $this->normalizeDurum($row[12] ?? null),
                'olusturulma_tarihi'  => $this->parseDt($row[13] ?? null),
                'guncellenme_tarihi'  => $this->parseDt($row[14] ?? null),
                'created_at'          => $now,
                'updated_at'          => $now,
            ];

            if (count($batch) >= 500) {
                DB::table('kuyular')->insert($batch);
                $inserted += count($batch);
                $this->line("  → {$inserted} kayıt eklendi...");
                $batch = [];
            }
        }

        fclose($handle);

        if ($batch) {
            DB::table('kuyular')->insert($batch);
            $inserted += count($batch);
        }

        $this->newLine();
        $this->info("✅ Import tamamlandı!");
        $this->table(
            ['Metrik', 'Değer'],
            [
                ['Eklenen kayıt', number_format($inserted)],
                ['Atlanan satır', number_format($skipped)],
            ]
        );

        return self::SUCCESS;
    }

    private function cleanStr($val, int $maxLen = null): ?string
    {
        if ($val === null) return null;
        $s = trim((string) $val);
        if (in_array($s, ['-', '0', ''], true)) return null;
        return $maxLen ? mb_substr($s, 0, $maxLen) : ($s === '' ? null : $s);
    }

    private function cleanDecimal($val): ?float
    {
        if ($val === null || trim((string)$val) === '' || trim((string)$val) === '-') return null;
        $v = str_replace(',', '.', trim((string) $val));
        return is_numeric($v) ? (float) $v : null;
    }

    private function normalizeDurum($val): string
    {
        if (!$val || trim($val) === '') return 'aktif';
        $s = strtolower(trim($val));
        return str_contains($s, 'pasif') ? 'pasif' : 'aktif';
    }

    private function parseDt($val): ?string
    {
        if (!$val || trim((string)$val) === '') return null;
        $s = trim((string) $val);
        foreach (['Y-m-d H:i', 'Y-m-d H:i:s', 'd.m.Y H:i', 'd.m.Y', 'Y-m-d'] as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $s);
            if ($dt && $dt->format($fmt) === $s) {
                return $dt->format('Y-m-d H:i:s');
            }
        }
        // Y-m-d H:i formatı format kontrolsüz dene
        $dt = \DateTime::createFromFormat('Y-m-d H:i', $s);
        if ($dt) return $dt->format('Y-m-d H:i:s');
        return null;
    }
}
