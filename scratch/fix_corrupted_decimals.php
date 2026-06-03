<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BeklemeKontrolHavuzu;
use App\Models\KesinlesenFatura;
use App\Models\Reaktifler;
use App\Models\ItirazEdilenler;
use App\Models\Aboneler;
use App\Services\ExcelImportService;
use Illuminate\Support\Facades\DB;

$service = app(ExcelImportService::class);

echo "Bozuk ondalık veriler düzeltiliyor...\n";

$fixTable = function($modelClass, $label) use ($service) {
    echo "$label tablosu kontrol ediliyor...\n";
    $count = 0;
    
    $modelClass::chunk(100, function($records) use ($service, &$count, $label) {
        foreach ($records as $record) {
            $payload = $record->payload;
            if (!$payload) continue;

            $updates = [];
            
            // Decimal alanları tekrar parse et
            $decimalFields = [
                't1_ilk_endeks', 't1_son_endeks', 't2_ilk_endeks', 't2_son_endeks', 't3_ilk_endeks', 't3_son_endeks',
                'ri_ilk_endeks', 'ri_son_endeks', 'ri_fark_endeks', 'rc_ilk_endeks', 'rc_son_endeks', 'rc_fark_endeks',
                't1_tuketim', 't2_tuketim', 't3_tuketim', 'trafo_kaybi_kwh', 'ek_tuketim', 'yillik_tuketim',
                'fatura_edilecek_toplam_tuketim_kwh', 'gunluk_ortalama_tuketim', 'birim_fiyat', 'dagitim_birim_fiyat',
                'aktif_tuketim_tl', 'dagitim_bedeli', 'dagitim_bedeli_ek', 'enerji_fonu', 'reaktif_tl',
                'acma_kapama_bedeli', 'gecikme_tutari', 'trt_fonu', 'btv', 'btv_orani', 'fatura_tutari',
                'fatura_tutari_ek', 'kdv', 'genel_toplam', 'tutar_toplam', 'carpan'
            ];

            // Payload içindeki eşleşen anahtarları bulmak için findField mantığını kullanmalıyız
            // Ya da ExcelImportService'deki promoToStaging mappingini taklit etmeliyiz.
            // Ama en basiti, mevcut record değerine bakıp eğer çok büyükse (örn > 1000000) mi kontrol etsek?
            // Hayır, en doğrusu tekrar maplemek.
            
            $newValues = $this_fix_map($record, $payload, $service);
            
            $needsUpdate = false;
            foreach ($newValues as $k => $v) {
                if ($record->$k != $v) {
                    $record->$k = $v;
                    $needsUpdate = true;
                }
            }

            if ($needsUpdate) {
                $record->save();
                $count++;
            }
        }
    });
    echo "$label bitti. $count kayıt düzeltildi.\n\n";
};

// Helper function to map fields from payload (ExcelImportService'den kopyalandı/uyarlandı)
function this_fix_map($record, $payload, $service) {
    
    $find = function($keys) use ($payload) {
        foreach ($payload as $k => $v) {
            foreach ($keys as $key) {
                if (mb_strtolower(trim($k), 'UTF-8') === mb_strtolower($key, 'UTF-8')) {
                    $val = $v !== null ? trim((string)$v) : null;
                    return ($val !== '') ? $val : null;
                }
            }
        }
        return null;
    };

    $parse = fn($val) => $service->parseDecimal($val);

    return [
        'carpan'          => $parse($find(['carpan', 'çarpan'])),
        't1_ilk_endeks'   => $parse($find(['t1_ilk_endeks', 't1 ilk endeks'])),
        't1_son_endeks'   => $parse($find(['t1_son_endeks', 't1 son endeks'])),
        't2_ilk_endeks'   => $parse($find(['t2_ilk_endeks', 't2 ilk endeks'])),
        't2_son_endeks'   => $parse($find(['t2_son_endeks', 't2 son endeks'])),
        't3_ilk_endeks'   => $parse($find(['t3_ilk_endeks', 't3 ilk endeks'])),
        't3_son_endeks'   => $parse($find(['t3_son_endeks', 't3 son endeks'])),
        'ri_ilk_endeks'   => $parse($find(['t4_ilk_endeks', 'ri_ilk_endeks', 'ri ilk endeks'])),
        'ri_son_endeks'   => $parse($find(['t4_son_endeks', 'ri_son_endeks', 'ri son endeks'])),
        'ri_fark_endeks'  => $parse($find(['t4_fark', 'ri_fark_endeks', 'ri fark'])),
        'rc_ilk_endeks'   => $parse($find(['t5_ilk_endeks', 'rc_ilk_endeks', 'rc ilk endeks'])),
        'rc_son_endeks'   => $parse($find(['t5_son_endeks', 'rc_son_endeks', 'rc son endeks'])),
        'rc_fark_endeks'  => $parse($find(['t5_fark', 'rc_fark_endeks', 'rc fark'])),
        't1_tuketim'      => $parse($find(['t1_fark', 't1_tuketim'])),
        't2_tuketim'      => $parse($find(['t2_fark', 't2_tuketim'])),
        't3_tuketim'      => $parse($find(['t3_fark', 't3_tuketim'])),
        'ek_tuketim'      => $parse($find(['aktif_miktar', 'aktif_kwh', 'ek tuketim'])),
        'fatura_edilecek_toplam_tuketim_kwh' => $parse($find(['aktif kwh', 'aktif_kwh'])),
        'birim_fiyat'         => $parse($find(['birim_fiyat', 'birim fiyat'])),
        'dagitim_birim_fiyat' => $parse($find(['dagitim_birim_fiyat', 'dagitim birim fiyat'])),
        'aktif_tuketim_tl'    => $parse($find(['akti̇f tüketi̇m', 'aktif tüketim', 'aktif tuketim'])),
        'dagitim_bedeli'      => $parse($find(['dagitim bedeli', 'dagitim_bedeli'])),
        'dagitim_bedeli_ek'   => $parse($find(['dagitim_bedeli_ek', 'dagitim bedeli ek'])),
        'enerji_fonu'         => $parse($find(['ee_fonu', 'enerji_fonu', 'enerji fonu'])),
        'reaktif_tl'          => $parse($find(['reakti̇f tüketi̇m', 'reaktif tüketim', 'reaktif_tl', 'reaktif_miktar'])),
        'acma_kapama_bedeli'  => $parse($find(['acma_kapama_bedeli', 'acma kapama bedeli'])),
        'gecikme_tutari'      => $parse($find(['devir_gecikme', 'gecikme_tutari', 'gecikme tutari'])),
        'trt_fonu'            => $parse($find(['trt_payi', 'trt fonu', 'trt_fonu'])),
        'btv'                 => $parse($find(['beledi̇ye vergi̇si̇', 'belediye vergisi', 'btv', 'b.t.v.'])),
        'btv_orani'           => $parse($find(['btv_orani', 'btv orani'])),
        'fatura_tutari'       => $parse($find(['toplam tutar', 'fatura_tutar'])),
        'fatura_tutari_ek'    => $parse($find(['fatura_tutari_ek', 'fatura tutari ek'])),
        'kdv'                 => $parse($find(['k.d.v.', 'kdv', 'k d v'])),
        'genel_toplam'        => $parse($find(['fatura_tutar', 'fatura tutari', 'toplam tutar'])),
        'tutar_toplam'        => $parse($find(['fatura_tutar', 'toplam tutar'])),
    ];
}

$fixTable(BeklemeKontrolHavuzu::class, 'Staging (Bekleme Havuzu)');
$fixTable(KesinlesenFatura::class, 'Kesinleşen Faturalar');
$fixTable(Reaktifler::class, 'Reaktifler');
$fixTable(ItirazEdilenler::class, 'İtiraz Edilenler');

// ── Aboneler tablosunu düzelt (En son güncel olanlardan) ──
echo "Aboneler tablosu düzeltiliyor (Çarpan vb.)...\n";
$aboneCount = 0;
Aboneler::chunk(100, function($aboneler) use (&$aboneCount, $service) {
    foreach ($aboneler as $abone) {
        // Bu aboneye ait en son faturayı bulalım
        $fatura = BeklemeKontrolHavuzu::where('tesisat_no', $abone->ABONE_TESIS_NO)->orderBy('id','desc')->first()
               ?? KesinlesenFatura::where('tesisat_no', $abone->ABONE_TESIS_NO)->orderBy('id','desc')->first();
        
        if ($fatura && $fatura->carpan) {
            if ($abone->carpan != $fatura->carpan) {
                $abone->carpan = $fatura->carpan;
                $abone->save();
                $aboneCount++;
            }
        }
    }
});
echo "Aboneler bitti. $aboneCount abone güncellendi.\n";

echo "\nTüm düzeltmeler tamamlandı!\n";
