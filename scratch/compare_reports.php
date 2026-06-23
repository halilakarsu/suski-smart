<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\KesinlesenFatura;
use Illuminate\Support\Facades\DB;

// Fetch all available periods in database
$periods = KesinlesenFatura::where('odeme_durumu', 'odendi')
    ->distinct()
    ->orderBy('donem', 'desc')
    ->pluck('donem')
    ->toArray();

echo "Available periods: " . implode(', ', $periods) . "\n\n";

// We'll define the expressions exactly as in ReportController
$tuketimExpr = 'CASE 
                    WHEN fatura_edilecek_toplam_tuketim_kwh IS NOT NULL 
                         AND fatura_edilecek_toplam_tuketim_kwh > 0 
                    THEN fatura_edilecek_toplam_tuketim_kwh 
                    ELSE (COALESCE(t1_tuketim,0) + COALESCE(t2_tuketim,0) + COALESCE(t3_tuketim,0) + COALESCE(ek_tuketim,0))
                END';

$tutarExpr = 'COALESCE(tutar_toplam, fatura_tutari, 0)';

$isKoyCondition = "EXISTS (
    SELECT 1 FROM aboneler
    WHERE aboneler.ABONE_TESIS_NO = kesinlesen_faturalar.tesisat_no
      AND aboneler.yerlesim_turu = 'KÖY'
)";

$isMerkezCondition = "NOT EXISTS (
    SELECT 1 FROM aboneler
    WHERE aboneler.ABONE_TESIS_NO = kesinlesen_faturalar.tesisat_no
      AND aboneler.yerlesim_turu = 'KÖY'
)";

foreach ($periods as $period) {
    echo "--- Period: $period ---\n";
    
    // Periodical Report totals for this period
    $periodicalQuery = KesinlesenFatura::where('odeme_durumu', 'odendi')
        ->where('donem', $period);
        
    $periodicalTotals = $periodicalQuery->selectRaw(
        "COUNT(*) as total_fatura,
         SUM({$tuketimExpr}) as total_tuketim,
         SUM({$tutarExpr}) as total_tutar"
    )->first();
    
    // Koy-Merkez Report totals for this period
    $koyMerkezQuery = KesinlesenFatura::where('odeme_durumu', 'odendi')
        ->where('donem', $period);
        
    // In koyMerkez, NormalizesIlce LEFT JOIN is applied!
    $koyMerkezQuery->leftJoin('bolgeler as bolgeler_norm', "bolgeler_norm.bolge_kodu", '=', "kesinlesen_faturalar.ilce_kodu");
    
    $koyMerkezTotals = $koyMerkezQuery->selectRaw("
        COUNT(*) as total_fatura,
        SUM(CASE WHEN {$isKoyCondition} THEN {$tuketimExpr} ELSE 0 END) as koy_tuketim,
        SUM(CASE WHEN {$isKoyCondition} THEN {$tutarExpr} ELSE 0 END) as koy_tutar,
        SUM(CASE WHEN {$isMerkezCondition} THEN {$tuketimExpr} ELSE 0 END) as merkez_tuketim,
        SUM(CASE WHEN {$isMerkezCondition} THEN {$tutarExpr} ELSE 0 END) as merkez_tutar
    ")->first();
    
    $koyMerkezCombinedTuketim = $koyMerkezTotals->koy_tuketim + $koyMerkezTotals->merkez_tuketim;
    $koyMerkezCombinedTutar = $koyMerkezTotals->koy_tutar + $koyMerkezTotals->merkez_tutar;
    
    echo "Periodical:   Count: {$periodicalTotals->total_fatura} | Tüketim: {$periodicalTotals->total_tuketim} | Tutar: {$periodicalTotals->total_tutar}\n";
    echo "Köy-Merkez:   Count: {$koyMerkezTotals->total_fatura} | Tüketim: {$koyMerkezCombinedTuketim} (K: {$koyMerkezTotals->koy_tuketim}, M: {$koyMerkezTotals->merkez_tuketim}) | Tutar: {$koyMerkezCombinedTutar} (K: {$koyMerkezTotals->koy_tutar}, M: {$koyMerkezTotals->merkez_tutar})\n";
    
    if ($periodicalTotals->total_fatura !== $koyMerkezTotals->total_fatura) {
        echo ">> MISMATCH in record count!\n";
    }
    if (abs($periodicalTotals->total_tuketim - $koyMerkezCombinedTuketim) > 0.01) {
        echo ">> MISMATCH in consumption! Diff: " . ($periodicalTotals->total_tuketim - $koyMerkezCombinedTuketim) . "\n";
    }
    if (abs($periodicalTotals->total_tutar - $koyMerkezCombinedTutar) > 0.01) {
        echo ">> MISMATCH in total amount! Diff: " . ($periodicalTotals->total_tutar - $koyMerkezCombinedTutar) . "\n";
    }
    echo "\n";
}
