<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename raw_invoice_data -> hamveri
        Schema::rename('raw_invoice_data', 'hamveri');

        // 2. Rename staging_invoices -> bekleme_kontrol_havuzu
        Schema::rename('staging_invoices', 'bekleme_kontrol_havuzu');

        // 3. Update bekleme_kontrol_havuzu schema
        Schema::table('bekleme_kontrol_havuzu', function (Blueprint $table) {
            // Rename the foreign key column to match new table name
            $table->renameColumn('raw_invoice_data_id', 'hamveri_id');
            
            // Add the 52 columns (skipping fatura_no, tesisat_no, tutar_toplam as they exist or will be replaced)
            // Existing in staging_invoices: fatura_no, tesisat_no, son_odeme_tarihi, tutar_toplam
            
            $table->string('sira_no')->nullable()->after('tesisat_no');
            $table->string('pmum_id')->nullable()->after('sira_no');
            $table->string('sayac_seri_no')->nullable()->after('pmum_id');
            $table->string('carpan')->nullable()->after('sayac_seri_no');
            $table->text('adres')->nullable()->after('carpan');
            $table->string('dagitim')->nullable()->after('adres');
            $table->string('t1_ilk_endeks')->nullable()->after('dagitim');
            $table->string('t2_ilk_endeks')->nullable()->after('t1_ilk_endeks');
            $table->string('t3_ilk_endeks')->nullable()->after('t2_ilk_endeks');
            $table->string('t0_ilk_endeks')->nullable()->after('t3_ilk_endeks');
            $table->string('t1_son_endeks')->nullable()->after('t0_ilk_endeks');
            $table->string('t2_son_endeks')->nullable()->after('t1_son_endeks');
            $table->string('t3_son_endeks')->nullable()->after('t2_son_endeks');
            $table->string('to_son_endeks')->nullable()->after('t3_son_endeks');
            $table->string('ri_ilk_endeks')->nullable()->after('to_son_endeks');
            $table->string('ri_son_endeks')->nullable()->after('ri_ilk_endeks');
            $table->string('ri_fark_endeks')->nullable()->after('ri_son_endeks');
            $table->string('rc_ilk_endeks')->nullable()->after('ri_fark_endeks');
            $table->string('rc_son_endeks')->nullable()->after('rc_ilk_endeks');
            $table->string('rc_fark_endeks')->nullable()->after('rc_son_endeks');
            $table->string('t1_tuketim')->nullable()->after('rc_fark_endeks');
            $table->string('t2_tuketim')->nullable()->after('t1_tuketim');
            $table->string('t3_tuketim')->nullable()->after('t2_tuketim');
            $table->string('trafo_kaybi_kwh')->nullable()->after('t3_tuketim');
            $table->string('ek_tuketim')->nullable()->after('trafo_kaybi_kwh');
            $table->string('ilce')->nullable()->after('ek_tuketim');
            $table->string('yillik_tuketim')->nullable()->after('ilce');
            $table->string('serbest_tuketici')->nullable()->after('yillik_tuketim');
            $table->string('fatura_edilecek_toplam_tuketim_kwh')->nullable()->after('serbest_tuketici');
            $table->string('tarife')->nullable()->after('fatura_edilecek_toplam_tuketim_kwh');
            $table->string('tarife_2')->nullable()->after('tarife');
            $table->string('ilk_okuma')->nullable()->after('tarife_2');
            $table->string('son_okuma')->nullable()->after('ilk_okuma');
            $table->string('birim_fiyat')->nullable()->after('son_okuma');
            $table->string('dagitim_birim_fiyat')->nullable()->after('birim_fiyat');
            $table->string('aktif_tuketim_tl')->nullable()->after('dagitim_birim_fiyat');
            $table->string('dagitim_bedeli_ek')->nullable()->after('aktif_tuketim_tl'); // dagitim_bedeli exists
            $table->string('enerji_fonu')->nullable()->after('dagitim_bedeli_ek');
            $table->string('reaktif_tl')->nullable()->after('enerji_fonu');
            $table->string('acma_kapama_bedeli')->nullable()->after('reaktif_tl');
            $table->string('gecikme_tutari')->nullable()->after('acma_kapama_bedeli');
            $table->string('trt_fonu')->nullable()->after('gecikme_tutari');
            $table->string('btv')->nullable()->after('trt_fonu');
            $table->string('fatura_tutari_ek')->nullable()->after('btv'); // tutar_toplam exists, but adding explicit from list
            $table->string('kdv')->nullable()->after('fatura_tutari_ek');
            $table->string('genel_toplam')->nullable()->after('kdv');
            $table->string('btv_orani')->nullable()->after('genel_toplam');
            $table->string('gunluk_ortalama_tuketim')->nullable()->after('btv_orani');
            $table->string('baglanti_grubu')->nullable()->after('gunluk_ortalama_tuketim');
            $table->string('ilce_kodu')->nullable()->after('baglanti_grubu');
        });
    }

    public function down(): void
    {
        Schema::table('bekleme_kontrol_havuzu', function (Blueprint $table) {
            $table->renameColumn('hamveri_id', 'raw_invoice_data_id');
            $table->dropColumn([
                'sira_no', 'pmum_id', 'sayac_seri_no', 'carpan', 'adres', 'dagitim',
                't1_ilk_endeks', 't2_ilk_endeks', 't3_ilk_endeks', 't0_ilk_endeks',
                't1_son_endeks', 't2_son_endeks', 't3_son_endeks', 'to_son_endeks',
                'ri_ilk_endeks', 'ri_son_endeks', 'ri_fark_endeks', 'rc_ilk_endeks',
                'rc_son_endeks', 'rc_fark_endeks', 't1_tuketim', 't2_tuketim',
                't3_tuketim', 'trafo_kaybi_kwh', 'ek_tuketim', 'ilce', 'yillik_tuketim',
                'serbest_tuketici', 'fatura_edilecek_toplam_tuketim_kwh', 'tarife',
                'tarife_2', 'ilk_okuma', 'son_okuma', 'birim_fiyat', 'dagitim_birim_fiyat',
                'aktif_tuketim_tl', 'dagitim_bedeli_ek', 'enerji_fonu', 'reaktif_tl',
                'acma_kapama_bedeli', 'gecikme_tutari', 'trt_fonu', 'btv',
                'fatura_tutari_ek', 'kdv', 'genel_toplam', 'btv_orani',
                'gunluk_ortalama_tuketim', 'baglanti_grubu', 'ilce_kodu'
            ]);
        });
        
        Schema::rename('bekleme_kontrol_havuzu', 'staging_invoices');
        Schema::rename('hamveri', 'raw_invoice_data');
    }
};
