<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BeklemeKontrolHavuzu;
use Illuminate\Support\Facades\DB;

echo "Bekleme havuzundaki tüm kayıtlar 'Onaylandı' durumuna getiriliyor...\n";

$count = BeklemeKontrolHavuzu::where('kontrol_edildi', false)
    ->update([
        'kontrol_edildi' => true,
        'kontrol_tarihi' => now()
    ]);

echo "$count adet kayıt onaylandı sekmesine (Kontrol Edildi) aktarıldı.\n";
