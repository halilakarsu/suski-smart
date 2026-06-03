<?php

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$missingIds = ['4992246', '6079209', '4249105', '5243403', '4244359', '6025519', '6134963', '6291139'];

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

foreach ($files as $fileConfig) {
    echo "Okunuyor: " . basename($fileConfig['path']) . "...\n";
    $data = Excel::toArray(new class {}, $fileConfig['path']);
    $rows = $data[0];

    foreach ($rows as $index => $row) {
        if ($index < $fileConfig['skip']) continue;

        $tesisat = trim($row[$fileConfig['map']['tesisat']] ?? '');
        if (in_array($tesisat, $missingIds)) {
            $tarife = trim($row[$fileConfig['map']['tarife']] ?? '');
            $abone_grubu = trim($row[$fileConfig['map']['abone_grubu']] ?? '');
            
            if (!empty($tarife) || !empty($abone_grubu)) {
                echo "Güncelleniyor $tesisat: Tarife=$tarife, Grup=$abone_grubu\n";
                DB::table('aboneler')->where('ABONE_TESIS_NO', $tesisat)->update([
                    'tarife' => $tarife,
                    'abone_grubu' => $abone_grubu,
                    'updated_at' => now()
                ]);
            }
        }
    }
}

echo "\nSon durum kontrolü:\n";
echo 'Kalan Eksik Tarife: ' . DB::table('aboneler')->where(function($q){ $q->whereNull('tarife')->orWhere('tarife', ''); })->count() . "\n";
