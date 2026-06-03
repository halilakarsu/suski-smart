<?php

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$files = [
    [
        'path' => __DIR__ . '/../storage/app/abone_koy_merkez.xlsx',
        'map' => ['tesisat' => 1, 'tarife' => 15, 'abone_grubu' => 16, 'unvan' => null, 'adres' => 2, 'bolge' => 4, 'yerlesim' => 3, 'og_durumu' => 5],
        'skip' => 1
    ],
    [
        'path' => __DIR__ . '/../storage/app/abone-gurup-adi.xlsx',
        'map' => ['tesisat' => 1, 'tarife' => 2, 'abone_grubu' => 3, 'unvan' => null, 'adres' => null, 'bolge' => 0, 'yerlesim' => null, 'og_durumu' => null],
        'skip' => 1
    ],
    [
        'path' => __DIR__ . '/../storage/app/aboneler_archive.xls',
        'map' => ['tesisat' => 3, 'tarife' => 8, 'abone_grubu' => 9, 'unvan' => null, 'adres' => 7, 'bolge' => 2, 'yerlesim' => null, 'og_durumu' => null],
        'skip' => 3
    ]
];

$masterData = [];

foreach ($files as $fileConfig) {
    if (!file_exists($fileConfig['path'])) {
        echo "Dosya bulunamadı: {$fileConfig['path']}\n";
        continue;
    }

    echo "Okunuyor: " . basename($fileConfig['path']) . "...\n";
    $data = Excel::toArray(new class {}, $fileConfig['path']);
    $rows = $data[0];

    foreach ($rows as $index => $row) {
        if ($index < $fileConfig['skip']) continue;

        $m = $fileConfig['map'];
        $tesisat = trim($row[$m['tesisat']] ?? '');
        if (empty($tesisat)) continue;

        // Eğer daha önce eklenmemişse veya eksik bilgi varsa doldur
        if (!isset($masterData[$tesisat])) {
            $masterData[$tesisat] = [
                'tesisat' => $tesisat,
                'tarife' => trim($row[$m['tarife']] ?? ''),
                'abone_grubu' => trim($row[$m['abone_grubu']] ?? ''),
                'adres' => $m['adres'] !== null ? trim($row[$m['adres']] ?? '') : null,
                'bolge' => $m['bolge'] !== null ? trim($row[$m['bolge']] ?? '') : null,
                'yerlesim' => $m['yerlesim'] !== null ? trim($row[$m['yerlesim']] ?? '') : null,
                'og_durumu' => $m['og_durumu'] !== null ? trim($row[$m['og_durumu']] ?? '') : null,
            ];
        } else {
            // Mevcut veride eksik varsa tamamla
            if (empty($masterData[$tesisat]['tarife'])) $masterData[$tesisat]['tarife'] = trim($row[$m['tarife']] ?? '');
            if (empty($masterData[$tesisat]['abone_grubu'])) $masterData[$tesisat]['abone_grubu'] = trim($row[$m['abone_grubu']] ?? '');
            if (empty($masterData[$tesisat]['adres']) && $m['adres'] !== null) $masterData[$tesisat]['adres'] = trim($row[$m['adres']] ?? '');
        }
    }
}

echo "Master data oluşturuldu. Toplam benzersiz tesisat: " . count($masterData) . "\n";

$updated = 0;
$inserted = 0;

foreach ($masterData as $tesisat => $info) {
    $abone = DB::table('aboneler')->where('ABONE_TESIS_NO', $tesisat)->first();

    if ($abone) {
        // Güncelle
        $update = [];
        if (empty($abone->tarife)) $update['tarife'] = $info['tarife'];
        if (empty($abone->abone_grubu)) $update['abone_grubu'] = $info['abone_grubu'];
        if (empty($abone->yerlesim_turu) && !empty($info['yerlesim'])) $update['yerlesim_turu'] = ($info['yerlesim'] == 'KÖY' ? 'KÖY' : 'MERKEZ');
        if (empty($abone->OG_durumu) && !empty($info['og_durumu'])) {
            $update['OG_durumu'] = ($info['og_durumu'] == 'OG' ? 1 : 0);
        }

        if (!empty($update)) {
            DB::table('aboneler')->where('id', $abone->id)->update($update);
            $updated++;
        }
    } else {
        // Yeni ekle
        // Fatura tablosundan ünvan çekmeye çalış
        $fatura = DB::table('kesinlesen_faturalar')->where('tesisat_no', $tesisat)->first();
        
        DB::table('aboneler')->insert([
            'ABONE_TESIS_NO' => $tesisat,
            'UNVAN' => $fatura->unvan ?? 'Bilinmeyen Abone',
            'ADRES' => $info['adres'] ?? ($fatura->adres ?? null),
            'BOLGE_ADI' => $info['bolge'] ?? ($fatura->ilce ?? null),
            'tarife' => $info['tarife'],
            'abone_grubu' => $info['abone_grubu'],
            'yerlesim_turu' => ($info['yerlesim'] == 'KÖY' ? 'KÖY' : 'MERKEZ'),
            'OG_durumu' => ($info['og_durumu'] == 'OG' ? 1 : 0),
            'is_new' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $inserted++;
    }
}

echo "\nİşlem Tamamlandı!\n";
echo "Bilgisi tamamlanan mevcut abone: $updated\n";
echo "Sisteme yeni eklenen abone: $inserted\n";
