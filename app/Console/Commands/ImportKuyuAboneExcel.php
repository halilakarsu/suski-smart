<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportKuyuAboneExcel extends Command
{
    protected $signature = 'import:kuyu-abone';

    protected $description = 'Import kuyu_no - abone_no pairs from Excel files in storage/app/import/';

    public function handle()
    {
        // Each file config: file, kw_col, ab_col, ilce_col, mahalle_col (-1 = none)
        $files = [
            ['file' => 'A-H-S KUYU ENVANTER (1).xlsx',           'kw' => 6,  'ab' => 0,  'ilce' => 2,  'mahalle' => 3],
            ['file' => 'K-B-H-B ENVANTER.xlsx',                   'kw' => 0,  'ab' => 4,  'ilce' => 1,  'mahalle' => 2],
            ['file' => 'SİVEREK ENVANTER.xlsx',                    'kw' => 0,  'ab' => 4,  'ilce' => 1,  'mahalle' => 2],
            ['file' => 'Viranşehie Envanter teslim edilecek.xlsx', 'kw' => 0,  'ab' => 1,  'ilce' => 2,  'mahalle' => 3],
            ['file' => 'Viranşehir Envarter 2.xlsx',                'kw' => 4,  'ab' => 3,  'ilce' => 0,  'mahalle' => 1],
            ['file' => 'envanter e-h-k.xlsx',                       'kw' => 15, 'ab' => 0,  'ilce' => 5,  'mahalle' => 6],
        ];

        $importDir = storage_path('app/import');
        $pairs = [];
        $aboneOnly = [];
        $seen = [];

        foreach ($files as $f) {
            $path = "$importDir/{$f['file']}";
            if (! file_exists($path)) {
                $this->warn("SKIP: {$f['file']} not found");

                continue;
            }

            $spreadsheet = IOFactory::load($path);
            $ws = $spreadsheet->getActiveSheet();
            $rows = $ws->toArray();
            $pairCount = 0;
            $aboneCount = 0;

            foreach ($rows as $i => $row) {
                if ($i === 0) {
                    continue;
                }

                $kw = preg_replace('/[^0-9]/', '', $row[$f['kw']] ?? '');
                $ab = preg_replace('/[^0-9]/', '', $row[$f['ab']] ?? '');
                $ilce = trim($row[$f['ilce']] ?? '');
                $mahalle = trim($row[$f['mahalle']] ?? '');

                if (! $ab) {
                    continue;
                }

                if ($kw) {
                    $key = "$kw|$ab";
                    if (! isset($seen[$key])) {
                        $pairs[] = ['kw' => $kw, 'ab' => $ab, 'ilce' => $ilce, 'mahalle' => $mahalle];
                        $seen[$key] = true;
                        $pairCount++;
                    }
                } else {
                    $key = "abonly|$ab";
                    if (! isset($seen[$key])) {
                        $aboneOnly[] = ['ab' => $ab, 'ilce' => $ilce, 'mahalle' => $mahalle];
                        $seen[$key] = true;
                        $aboneCount++;
                    }
                }
            }

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            $this->info("{$f['file']}: $pairCount pair(s), $aboneCount abone-only");
        }

        $this->line("\nTotal: ".count($pairs).' pairs, '.count($aboneOnly).' abone-only');

        // ── Update existing kuyular with abone_no ──
        $updated = 0;
        $already = 0;
        $notfound = 0;
        $created = 0;

        foreach ($pairs as $p) {
            $row = DB::table('kuyular')->where('kuyu_no', $p['kw'])->first(['id', 'abone_no']);
            if ($row) {
                if (! $row->abone_no) {
                    DB::table('kuyular')->where('id', $row->id)->update(['abone_no' => $p['ab']]);
                    $updated++;
                } else {
                    $already++;
                }
            } else {
                $notfound++;
                $id = DB::table('kuyular')->insertGetId([
                    'kuyu_no' => $p['kw'],
                    'abone_no' => $p['ab'],
                    'ilce' => $p['ilce'] ?: null,
                    'adres' => $p['mahalle'] ?: null,
                    'durum' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $created++;
            }
        }

        // ── Create new kuyular for abone-only records ──
        $aboneCreated = 0;
        $aboneSkipped = 0;
        foreach ($aboneOnly as $p) {
            $exists = DB::table('kuyular')->where('abone_no', $p['ab'])->exists();
            if (! $exists) {
                DB::table('kuyular')->insert([
                    'abone_no' => $p['ab'],
                    'ilce' => $p['ilce'] ?: null,
                    'adres' => $p['mahalle'] ?: null,
                    'durum' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $aboneCreated++;
            } else {
                $aboneSkipped++;
            }
        }

        $this->line("\n--- Kuyulu Kayıtlar ---");
        $this->info("Updated (abone_no eklendi): $updated");
        $this->line("Already had abone_no: $already");
        $this->info("Created (yeni kuyu eklendi): $created");
        $this->line("Kuyu not found (oluşturuldu): $notfound");

        $this->line("\n--- Abone-Only Kayıtlar ---");
        $this->info("Created (yeni kuyu eklendi): $aboneCreated");
        $this->line("Already exists: $aboneSkipped");
    }
}
