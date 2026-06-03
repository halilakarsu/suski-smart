<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cols = [
            'T1_ILK_ENDEKS', 'T1_SON_ENDEKS', 'T2_ILK_ENDEKS', 'T2_SON_ENDEKS',
            'T3_ILK_ENDEKS', 'T3_SON_ENDEKS', 'T0_ILK_ENDEKS', 'T0_SON_ENDEKS',
            'ENDUKTIF_ILK_ENDEKS', 'ENDUKTIF_SON_ENDEKS', 'KAPASIF_ILK_ENDEKS', 'KAPASIF_SON_ENDEKS',
            'ENDUKTIF_TUKETIM', 'KAPASIF_TUKETIM',
            'RI_ILK_ENDEKS', 'RI_SON_ENDEKS', 'RC_ILK_ENDEKS', 'RC_SON_ENDEKS',
            'RI_ENDEKS_FARKI', 'RC_ENDEKS_FARKI',
            'ENDUKTIF_BEDEL', 'KAPASIF_BEDEL', 'REAKTIF_TOPLAM_BEDEL', 'AKTIF_TOPLAM_BEDEL',
            'GUNLUK_ORTALAMA_TUKETIM', 'YILLIK_ORTALAMA_TUKETIM', 'DAGITIM_BEDEL',
            'ENERJI_FONU', 'TRT_FONU', 'ACMA_KAPAMA', 'BELEDIYE_TUKETIM_VERGISI',
            'FATURA_TUTARI', 'KATMA_DEGER_VERGISI', 'GENEL_TOPLAM', 'YUVARLAMA',
            'DAMGA_VERGISI', 'GECIKME_BEDELI', 'ODENECEK_TUTAR', 'AKTIF_ENERJI_BEDEL'
        ];

        foreach ($cols as $col) {
            if (Schema::hasColumn('faturalar', $col)) {
                // Veriyi temizle: virgülü noktaya çevir, boşlukları null yap
                DB::statement("UPDATE `faturalar` SET `$col` = REPLACE(`$col`, ',', '.')");
                DB::statement("UPDATE `faturalar` SET `$col` = NULL WHERE `$col` = '' OR `$col` NOT REGEXP '^[0-9.-]+$'");
                
                // faturalar tablosu çok büyük ve veriler kirli olduğu için DOUBLE daha esnek ve yeterince hassastır.
                DB::statement("ALTER TABLE `faturalar` MODIFY `$col` DOUBLE NULL");
            }
        }
    }

    public function down(): void
    {
        // No easy way back, skipping revert logic for brevity as it's a fix
    }
};
