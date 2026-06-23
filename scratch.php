<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test: bolge filtresi olmadan koyMerkez vs periodical aynı sonucu veriyor mu?
$period = '2026-05';

// KoyMerkez query
$controller = new App\Http\Controllers\ReportController();

$req1 = Illuminate\Http\Request::create('/admin/reports/koy-merkez', 'GET', ['start_period' => $period]);
$res1 = $controller->koyMerkez($req1);
$d1 = $res1->getData();
$kmTotal = $d1['totalKoyTutar'] + $d1['totalMerkezTutar'];
$kmTuketim = $d1['totalKoyTuketim'] + $d1['totalMerkezTuketim'];

// Periodical query - same structure
DB::enableQueryLog();
$req2 = Illuminate\Http\Request::create('/admin/reports/periodical', 'GET', ['start_period' => $period]);
$res2 = $controller->periodical($req2);
$d2 = $res2->getData();
$perTotal = $d2['totals']->total_tutar ?? 0;
$perTuketim = $d2['totals']->total_tuketim ?? 0;
$log = DB::getQueryLog();

echo "=== MAYIS 2026-05 ===\n";
echo "KoyMerkez Toplam Tutar: " . number_format($kmTotal, 2) . "\n";
echo "KoyMerkez Toplam Tüketim: " . number_format($kmTuketim, 2) . "\n";
echo "Periodical Toplam Tutar: " . number_format($perTotal, 2) . "\n";
echo "Periodical Toplam Tüketim: " . number_format($perTuketim, 2) . "\n";
echo "Fark (Tutar): " . number_format($perTotal - $kmTotal, 2) . "\n";
echo "Fark (Tüketim): " . number_format($perTuketim - $kmTuketim, 2) . "\n";

// Tüm dönemler için
echo "\n=== TÜM DÖNEMLER ===\n";
$donemler = DB::table('kesinlesen_faturalar')->where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');
foreach($donemler as $d) {
    $req = Illuminate\Http\Request::create('/admin/reports/koy-merkez', 'GET', ['start_period' => $d]);
    $res = $controller->koyMerkez($req);
    $data = $res->getData();
    $kmT = $data['totalKoyTutar'] + $data['totalMerkezTutar'];

    $perQ = DB::table('kesinlesen_faturalar')
        ->where('odeme_durumu', 'odendi')
        ->where('donem', $d)
        ->sum(DB::raw('COALESCE(tutar_toplam, fatura_tutari, 0)'));

    $diff = $perQ - $kmT;
    echo "Dönem: $d | KM: " . number_format($kmT, 2) . " | DB: " . number_format($perQ, 2) . " | Fark: " . number_format($diff, 2) . "\n";
}

