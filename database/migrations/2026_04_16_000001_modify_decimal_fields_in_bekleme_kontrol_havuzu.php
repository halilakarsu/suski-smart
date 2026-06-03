<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Para alanlarını string'den decimal(18,2)'ye, sayısal alanları da decimal(12,2)'ye dönüştür
     */
    public function up(): void
    {
        // Mevcut string veriler NULL'a set et, sonra column type'ını değiştir
        DB::table('bekleme_kontrol_havuzu')->update([
            'birim_fiyat' => null,
            'dagitim_birim_fiyat' => null,
            'aktif_tuketim_tl' => null,
            'dagitim_bedeli_ek' => null,
            'enerji_fonu' => null,
            'reaktif_tl' => null,
            'acma_kapama_bedeli' => null,
            'gecikme_tutari' => null,
            'trt_fonu' => null,
            'btv' => null,
            'fatura_tutari_ek' => null,
            'kdv' => null,
            'genel_toplam' => null,
            'btv_orani' => null,
            't1_ilk_endeks' => null,
            't2_ilk_endeks' => null,
            't3_ilk_endeks' => null,
            't0_ilk_endeks' => null,
            't1_son_endeks' => null,
            't2_son_endeks' => null,
            't3_son_endeks' => null,
            'to_son_endeks' => null,
            'ri_ilk_endeks' => null,
            'ri_son_endeks' => null,
            'ri_fark_endeks' => null,
            'rc_ilk_endeks' => null,
            'rc_son_endeks' => null,
            'rc_fark_endeks' => null,
            't1_tuketim' => null,
            't2_tuketim' => null,
            't3_tuketim' => null,
            'trafo_kaybi_kwh' => null,
            'ek_tuketim' => null,
            'yillik_tuketim' => null,
            'fatura_edilecek_toplam_tuketim_kwh' => null,
            'gunluk_ortalama_tuketim' => null,
            'carpan' => null,
        ]);

        // Raw SQL ile column types'ı değiştir
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `birim_fiyat` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `dagitim_birim_fiyat` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `aktif_tuketim_tl` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `dagitim_bedeli_ek` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `enerji_fonu` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `reaktif_tl` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `acma_kapama_bedeli` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `gecikme_tutari` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `trt_fonu` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `btv` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `fatura_tutari_ek` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `kdv` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `genel_toplam` DECIMAL(18, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `btv_orani` DECIMAL(18, 2) NULL');
        
        // Sayısal alanlar (12,2)
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `t1_ilk_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `t2_ilk_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `t3_ilk_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `t0_ilk_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `t1_son_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `t2_son_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `t3_son_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `to_son_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `ri_ilk_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `ri_son_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `ri_fark_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `rc_ilk_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `rc_son_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `rc_fark_endeks` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `t1_tuketim` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `t2_tuketim` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `t3_tuketim` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `trafo_kaybi_kwh` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `ek_tuketim` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `yillik_tuketim` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `fatura_edilecek_toplam_tuketim_kwh` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `gunluk_ortalama_tuketim` DECIMAL(12, 2) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `carpan` DECIMAL(12, 2) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bekleme_kontrol_havuzu', function (Blueprint $table) {
            // Para alanlarını geri string'e çevir
            $table->string('birim_fiyat')->nullable()->change();
            $table->string('dagitim_birim_fiyat')->nullable()->change();
            $table->string('aktif_tuketim_tl')->nullable()->change();
            $table->string('dagitim_bedeli_ek')->nullable()->change();
            $table->string('enerji_fonu')->nullable()->change();
            $table->string('reaktif_tl')->nullable()->change();
            $table->string('acma_kapama_bedeli')->nullable()->change();
            $table->string('gecikme_tutari')->nullable()->change();
            $table->string('trt_fonu')->nullable()->change();
            $table->string('btv')->nullable()->change();
            $table->string('fatura_tutari_ek')->nullable()->change();
            $table->string('kdv')->nullable()->change();
            $table->string('genel_toplam')->nullable()->change();
            $table->string('btv_orani')->nullable()->change();

            // Sayısal alanları geri string'e çevir
            $table->string('t1_ilk_endeks')->nullable()->change();
            $table->string('t2_ilk_endeks')->nullable()->change();
            $table->string('t3_ilk_endeks')->nullable()->change();
            $table->string('t0_ilk_endeks')->nullable()->change();
            $table->string('t1_son_endeks')->nullable()->change();
            $table->string('t2_son_endeks')->nullable()->change();
            $table->string('t3_son_endeks')->nullable()->change();
            $table->string('to_son_endeks')->nullable()->change();
            $table->string('ri_ilk_endeks')->nullable()->change();
            $table->string('ri_son_endeks')->nullable()->change();
            $table->string('ri_fark_endeks')->nullable()->change();
            $table->string('rc_ilk_endeks')->nullable()->change();
            $table->string('rc_son_endeks')->nullable()->change();
            $table->string('rc_fark_endeks')->nullable()->change();
            $table->string('t1_tuketim')->nullable()->change();
            $table->string('t2_tuketim')->nullable()->change();
            $table->string('t3_tuketim')->nullable()->change();
            $table->string('trafo_kaybi_kwh')->nullable()->change();
            $table->string('ek_tuketim')->nullable()->change();
            $table->string('yillik_tuketim')->nullable()->change();
            $table->string('fatura_edilecek_toplam_tuketim_kwh')->nullable()->change();
            $table->string('gunluk_ortalama_tuketim')->nullable()->change();
            $table->string('carpan')->nullable()->change();
        });
    }
};
