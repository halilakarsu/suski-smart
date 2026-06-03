<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kesinlesen_faturalar', function (Blueprint $table) {
            // Renames to match BeklemeKontrolHavuzu
            if (Schema::hasColumn('kesinlesen_faturalar', 'aktif_tuketim_bedeli')) {
                $table->renameColumn('aktif_tuketim_bedeli', 'aktif_tuketim_tl');
            }
            if (Schema::hasColumn('kesinlesen_faturalar', 'kdv_tutari')) {
                $table->renameColumn('kdv_tutari', 'kdv');
            }
            if (Schema::hasColumn('kesinlesen_faturalar', 'toplam_odenecek_tutar')) {
                $table->renameColumn('toplam_odenecek_tutar', 'genel_toplam');
            }

            // Missing Columns from BeklemeKontrolHavuzu
            $table->foreignId('hamveri_id')->nullable()->after('import_log_id');
            $table->string('kayit_durumu')->default('yeni')->after('hamveri_id');
            $table->string('hesap_adi')->nullable()->after('tesisat_no');
            $table->boolean('kontrol_edildi')->default(false)->after('kayit_durumu');
            $table->timestamp('kontrol_tarihi')->nullable()->after('kontrol_edildi');
            
            $table->string('sira_no')->nullable()->after('fatura_no');
            $table->string('pmum_id')->nullable()->after('sira_no');
            $table->string('sayac_seri_no')->nullable()->after('pmum_id');
            $table->decimal('carpan', 15, 2)->nullable()->after('sayac_seri_no');
            $table->string('dagitim')->nullable()->after('adres');
            $table->string('ilce_kodu')->nullable()->after('ilce');
            $table->string('baglanti_grubu')->nullable()->after('ilce_kodu');
            $table->string('tarife')->nullable()->after('baglanti_grubu');
            $table->string('tarife_2')->nullable()->after('tarife');
            $table->date('ilk_okuma')->nullable()->after('son_odeme_tarihi');
            $table->date('son_okuma')->nullable()->after('ilk_okuma');
            
            // Endekses
            $table->decimal('t1_ilk_endeks', 15, 2)->default(0)->after('son_okuma');
            $table->decimal('t1_son_endeks', 15, 2)->default(0)->after('t1_ilk_endeks');
            $table->decimal('t2_ilk_endeks', 15, 2)->default(0)->after('t1_son_endeks');
            $table->decimal('t2_son_endeks', 15, 2)->default(0)->after('t2_ilk_endeks');
            $table->decimal('t3_ilk_endeks', 15, 2)->default(0)->after('t2_son_endeks');
            $table->decimal('t3_son_endeks', 15, 2)->default(0)->after('t3_ilk_endeks');
            $table->decimal('t0_ilk_endeks', 15, 2)->default(0)->after('t3_son_endeks');
            $table->decimal('to_son_endeks', 15, 2)->default(0)->after('t0_ilk_endeks');
            
            $table->decimal('ri_ilk_endeks', 15, 2)->default(0)->after('to_son_endeks');
            $table->decimal('ri_son_endeks', 15, 2)->default(0)->after('ri_ilk_endeks');
            $table->decimal('ri_fark_endeks', 15, 2)->default(0)->after('ri_son_endeks');
            $table->decimal('rc_ilk_endeks', 15, 2)->default(0)->after('ri_fark_endeks');
            $table->decimal('rc_son_endeks', 15, 2)->default(0)->after('rc_ilk_endeks');
            $table->decimal('rc_fark_endeks', 15, 2)->default(0)->after('rc_son_endeks');
            
            // Consumptions
            $table->decimal('t1_tuketim', 15, 2)->default(0)->after('rc_fark_endeks');
            $table->decimal('t2_tuketim', 15, 2)->default(0)->after('t1_tuketim');
            $table->decimal('t3_tuketim', 15, 2)->default(0)->after('t2_tuketim');
            $table->decimal('trafo_kaybi_kwh', 15, 2)->default(0)->after('t3_tuketim');
            $table->decimal('ek_tuketim', 15, 2)->default(0)->after('trafo_kaybi_kwh');
            $table->decimal('yillik_tuketim', 15, 2)->default(0)->after('ek_tuketim');
            $table->decimal('gunluk_ortalama_tuketim', 15, 2)->default(0)->after('fatura_edilecek_toplam_tuketim_kwh');
            
            // Prices & Fees
            $table->decimal('birim_fiyat', 15, 5)->default(0)->after('gunluk_ortalama_tuketim');
            $table->decimal('dagitim_birim_fiyat', 15, 5)->default(0)->after('birim_fiyat');
            $table->decimal('dagitim_bedeli', 15, 2)->default(0)->after('aktif_tuketim_tl');
            $table->decimal('dagitim_bedeli_ek', 15, 2)->default(0)->after('dagitim_bedeli');
            $table->decimal('enerji_fonu', 15, 2)->default(0)->after('dagitim_bedeli_ek');
            $table->decimal('reaktif_tl', 15, 2)->default(0)->after('enerji_fonu');
            $table->decimal('acma_kapama_bedeli', 15, 2)->default(0)->after('reaktif_tl');
            $table->decimal('gecikme_tutari', 15, 2)->default(0)->after('acma_kapama_bedeli');
            $table->decimal('trt_fonu', 15, 2)->default(0)->after('gecikme_tutari');
            $table->decimal('btv', 15, 2)->default(0)->after('trt_fonu');
            $table->decimal('btv_orani', 15, 2)->default(0)->after('btv');
            $table->decimal('fatura_tutari', 15, 2)->default(0)->after('btv_orani');
            $table->decimal('fatura_tutari_ek', 15, 2)->default(0)->after('fatura_tutari');
            $table->decimal('tutar_toplam', 15, 2)->default(0)->after('genel_toplam');
            
            $table->boolean('serbest_tuketici')->default(false)->after('yillik_tuketim');
            $table->string('current_row_hash')->nullable()->after('tutar_toplam');
            $table->json('payload')->nullable()->after('current_row_hash');
        });
    }

    public function down(): void
    {
        Schema::table('kesinlesen_faturalar', function (Blueprint $table) {
            // Add down logic if needed, but since we're just adding a bunch of columns...
        });
    }
};
