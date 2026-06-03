<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['bekleme_kontrol_havuzu', 'kesinlesen_faturalar', 'itiraz_edilenler', 'reaktifler'];
        $cols = [
            't1_ilk_endeks', 't2_ilk_endeks', 't3_ilk_endeks', 't0_ilk_endeks',
            't1_son_endeks', 't2_son_endeks', 't3_son_endeks', 'to_son_endeks',
            'ri_ilk_endeks', 'ri_son_endeks', 'ri_fark_endeks',
            'rc_ilk_endeks', 'rc_son_endeks', 'rc_fark_endeks',
            't1_tuketim', 't2_tuketim', 't3_tuketim',
            'trafo_kaybi_kwh', 'ek_tuketim', 'yillik_tuketim', 
            'fatura_edilecek_toplam_tuketim_kwh', 'gunluk_ortalama_tuketim', 'carpan'
        ];

        foreach ($tables as $table) {
            foreach ($cols as $col) {
                // Precision increased to 6 decimal places to satisfy "exact as excel" requirement while being numeric
                DB::statement("ALTER TABLE `$table` MODIFY `$col` DECIMAL(20, 6) NULL");
            }
        }
    }

    public function down(): void
    {
        // No easy way back to varying precision, but let's go to 2 as it was before
        $tables = ['bekleme_kontrol_havuzu', 'kesinlesen_faturalar', 'itiraz_edilenler', 'reaktifler'];
        $cols = [
            't1_ilk_endeks', 't2_ilk_endeks', 't3_ilk_endeks', 't0_ilk_endeks',
            't1_son_endeks', 't2_son_endeks', 't3_son_endeks', 'to_son_endeks',
            'ri_ilk_endeks', 'ri_son_endeks', 'ri_fark_endeks',
            'rc_ilk_endeks', 'rc_son_endeks', 'rc_fark_endeks',
            't1_tuketim', 't2_tuketim', 't3_tuketim',
            'trafo_kaybi_kwh', 'ek_tuketim', 'yillik_tuketim', 
            'fatura_edilecek_toplam_tuketim_kwh', 'gunluk_ortalama_tuketim', 'carpan'
        ];

        foreach ($tables as $table) {
            foreach ($cols as $col) {
                DB::statement("ALTER TABLE `$table` MODIFY `$col` DECIMAL(12, 2) NULL");
            }
        }
    }
};
