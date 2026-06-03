<?php

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$filePath = __DIR__ . '/../storage/app/abone-gurup-adi.xlsx';

try {
    echo "Excel dosyası okunuyor...\n";
    $data = Excel::toArray(new class {}, $filePath);
    $rows = $data[0];
    
    $total = count($rows) - 1;
    $updated = 0;
    $notFound = 0;

    echo "Güncelleme başlatılıyor (Toplam $total satır)...\n";

    foreach ($rows as $index => $row) {
        if ($index == 0) continue; // Header atla
        
        $tesisat = trim($row[1]);
        $tarife = trim($row[2]);
        $abone_grubu = trim($row[3]);

        if (empty($tesisat)) continue;

        $affected = DB::table('aboneler')
            ->where('ABONE_TESIS_NO', $tesisat)
            ->update([
                'tarife' => $tarife,
                'abone_grubu' => $abone_grubu,
                'updated_at' => now()
            ]);

        if ($affected) {
            $updated++;
        } else {
            $notFound++;
        }

        if ($index % 500 == 0) {
            echo "İşlenen: $index / $total...\n";
        }
    }

    echo "\nİşlem tamamlandı!\n";
    echo "Başarıyla güncellenen: $updated\n";
    echo "Eşleşmeyen/Değişmeyen: $notFound\n";

} catch (\Exception $e) {
    echo "Hata: " . $e->getMessage() . "\n";
}
