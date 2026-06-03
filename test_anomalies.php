<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ImportLog;
use App\Models\BeklemeKontrolHavuzu;
use App\Models\Hamveri;
use App\Services\ExcelImportService;
use Illuminate\Support\Facades\DB;

echo "--- ANOMALI TESTLERI BASLIYOR ---\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
BeklemeKontrolHavuzu::truncate();
Hamveri::truncate();
ImportLog::truncate();
\App\Models\Aboneler::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$service = app(ExcelImportService::class);

$importLogMock = ImportLog::create([
    'user_id' => 1,
    'dosya_adi' => 'mock.xlsx',
    'orijinal_adi' => 'mock.xlsx',
    'donem' => '2025-10',
    'yol' => 'mock.xlsx',
    'dosya_hash' => hash('md5', 'mock_1'),
    'durum' => 'tamamlandi',
    'disk' => 'local_public',
]);

// AY 1 (NORMAL FATURA)
$basePayload = [
    'tesisat_no' => '12345',
    't1_ilk_endeks' => '100',
    't1_son_endeks' => '150',
    'reaktif_tl' => '0',
    'abone_grup_adi' => 'TİCARETHANE',
    'aktif_kwh' => '500',
    'carpan' => '1',
    'bolge_adi' => 'TEST_BOLGE'
];

Hamveri::create([
    'import_log_id' => $importLogMock->id,
    'fatura_no' => 'FAT001',
    'row_hash' => hash('md5', 'FAT001'),
    'payload' => $basePayload
]);

echo "1. Ayın Normal Faturası Staging'e Aktarılıyor...\n";
$service->promoteToStaging($importLogMock);
$havuz1 = BeklemeKontrolHavuzu::where('fatura_no', 'FAT001')->first();
echo "-> 1. Ay Anomaliler: " . json_encode($havuz1->payload['_anomaliler'] ?? []) . "\n";


// AY 2 - Çeşitli Anomaliler için Yeni Hamveriler Ekliyoruz
// a) Negatif Tüketim (İlk > Son)
Hamveri::create([
    'import_log_id' => $importLogMock->id,
    'fatura_no' => 'FAT002_NEG',
    'row_hash' => hash('md5', 'FAT002_NEG'),
    'payload' => array_merge($basePayload, [
        'tesisat_no' => '99999',
        't1_ilk_endeks' => '200',
        't1_son_endeks' => '150', // NEGATİF (150 < 200)
    ])
]);

// b) Sıçrama, Tarife, Çarpan, Reaktif ve Sıfır Tesisatları
Hamveri::create([
    'import_log_id' => $importLogMock->id,
    'fatura_no' => 'FAT_ANOM_KOMBO',
    'row_hash' => hash('md5', 'FAT_ANOM_KOMBO'),
    'payload' => array_merge($basePayload, [ // 12345 Tesisatı İçin
        'abone_grup_adi' => 'MESKEN', // Tarife değişimi (Önceki Ticarethane idi)
        'aktif_kwh' => '2000', // Anormal tüketim (500 -> 2000)
        'carpan' => '500', // Çarpan değişimi (1 -> 500)
        'reaktif_tl' => '120.5', // Reaktif ceza (0 -> 120.5)
    ])
]);

// c) Sıfır Tüketim (Farklı bir tesisat 1. Ay 500 tüketmiş olsun, 2. Ay 0 tüketsin)
$basePayloadSifir = array_merge($basePayload, ['tesisat_no' => '55555', 'aktif_kwh' => '80']);
Hamveri::create(['import_log_id' => $importLogMock->id, 'fatura_no' => 'FAT001_S', 'row_hash' => hash('md5', 'S1'), 'payload' => $basePayloadSifir]);
$service->promoteToStaging($importLogMock);

// 2. Ay faturası (Sıfır Tüketim)
Hamveri::create([
    'import_log_id' => $importLogMock->id,
    'fatura_no' => 'FAT002_S',
    'row_hash' => hash('md5', 'S2'),
    'payload' => array_merge($basePayloadSifir, [
        'aktif_kwh' => '0'
    ])
]);

echo "2. Ayın İhlalli Faturaları Staging'e Aktarılıyor...\n";
$service->promoteToStaging($importLogMock);

// SONUCLAR:
$negatifFatura = BeklemeKontrolHavuzu::where('fatura_no', 'FAT002_NEG')->first();
echo "\n-> Negatif Tüketim Testi Anomalileri: " . json_encode($negatifFatura->payload['_anomaliler'] ?? []) . "\n";

$komboFatura = BeklemeKontrolHavuzu::where('fatura_no', 'FAT_ANOM_KOMBO')->first();
echo "-> Kombo Anomali Testi (Tarife, Tüketim Sıçrama, Çarpan, Reaktif): " . json_encode($komboFatura->payload['_anomaliler'] ?? []) . "\n";

$sifirFatura = BeklemeKontrolHavuzu::where('fatura_no', 'FAT002_S')->first();
echo "-> Ani Sıfır Tüketim Testi Anomalileri: " . json_encode($sifirFatura->payload['_anomaliler'] ?? []) . "\n";

echo "\n--- TEST TAMAMLANDI ---\n";
