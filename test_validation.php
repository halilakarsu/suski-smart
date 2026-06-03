<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ImportLog;
use App\Models\BeklemeKontrolHavuzu;
use App\Services\ExcelImportService;
use Illuminate\Support\Facades\DB;

$service = app(ExcelImportService::class);

echo "--- ŞABLON DOĞRULAMA TESTİ BAŞLIYOR ---\n";

$importLog4 = ImportLog::create([
    'user_id'      => 1,
    'dosya_adi'    => '202602.xlsx', // Bu dosya farklı bir format mı? Göreceğiz.
    'orijinal_adi' => '202602.xlsx',
    'donem'        => '2026-02',
    'yol'          => 'public/ornek faturalar/202602.xlsx',
    'dosya_hash'   => hash('sha256', 'mock_hash_4'),
    'durum'        => 'isleniyor',
    'disk'         => 'local_public',
]);

try {
    $stats = $service->importToRaw($importLog4);
    echo "Başarılı Yüklendi! (Herhangi bir şablon kopukluğu yok)\n";
} catch (\Exception $e) {
    echo "BEKLENEN HATA YAKALANDI: \n";
    echo $e->getMessage() . "\n";
}
