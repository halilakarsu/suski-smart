<?php

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$filePath = __DIR__ . '/../storage/app/abone-gurup-adi.xlsx';

try {
    $data = Excel::toArray(new class {}, $filePath);
    $rows = $data[0];
    
    $notFoundTesisats = [];

    foreach ($rows as $index => $row) {
        if ($index == 0) continue;
        
        $tesisat = trim($row[1]);
        if (empty($tesisat)) continue;

        $exists = DB::table('aboneler')->where('ABONE_TESIS_NO', $tesisat)->exists();

        if (!$exists) {
            $notFoundTesisats[] = [
                'tesisat' => $tesisat,
                'tarife' => trim($row[2]),
                'abone_grubu' => trim($row[3]),
                'bolge' => trim($row[0])
            ];
        }
    }

    echo "Eşleşmeyen Tesisat Sayısı: " . count($notFoundTesisats) . "\n\n";

    if (count($notFoundTesisats) > 0) {
        echo "Fatura tablosu kontrol ediliyor...\n";
        
        foreach ($notFoundTesisats as $item) {
            $fatura = DB::table('kesinlesen_faturalar')
                ->where('tesisat_no', $item['tesisat'])
                ->first();
            
            if ($fatura) {
                echo "Bulundu (Faturası Var): " . $item['tesisat'] . " - " . ($fatura->unvan ?? 'Ünvan Yok') . " (İlçe: " . $fatura->ilce . ")\n";
                
                // İsterseniz burada yeni abone kaydı oluşturabiliriz.
                /*
                DB::table('aboneler')->insert([
                    'ABONE_TESIS_NO' => $item['tesisat'],
                    'UNVAN' => $fatura->unvan,
                    'BOLGE_ADI' => $item['bolge'],
                    'ADRES' => $fatura->adres,
                    'tarife' => $item['tarife'],
                    'abone_grubu' => $item['abone_grubu'],
                    'is_new' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                */
            } else {
                echo "Bulunamadı (Fatura da Yok): " . $item['tesisat'] . "\n";
            }
        }
    }

} catch (\Exception $e) {
    echo "Hata: " . $e->getMessage() . "\n";
}
