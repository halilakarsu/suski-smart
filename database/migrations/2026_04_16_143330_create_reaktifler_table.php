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
        Schema::create('reaktifler', function (Blueprint $table) {
            $table->id();
            $table->string('tesisat_no')->index();
            $table->string('fatura_no')->index();
            $table->string('sira_no')->nullable();
            $table->string('pmum_id')->nullable();
            $table->string('sayac_seri_no')->nullable();
            $table->decimal('carpan', 8, 4)->nullable();
            $table->text('adres')->nullable();
            $table->string('dagitim')->nullable();
            
            // T1, T2, T3, T0 tüketim alanları
            $table->decimal('t1_ilk_endeks', 15, 2)->nullable();
            $table->decimal('t2_ilk_endeks', 15, 2)->nullable();
            $table->decimal('t3_ilk_endeks', 15, 2)->nullable();
            $table->decimal('t0_ilk_endeks', 15, 2)->nullable();
            $table->decimal('t1_son_endeks', 15, 2)->nullable();
            $table->decimal('t2_son_endeks', 15, 2)->nullable();
            $table->decimal('t3_son_endeks', 15, 2)->nullable();
            $table->decimal('to_son_endeks', 15, 2)->nullable();
            
            // Reaktif endeks alanları
            $table->decimal('ri_ilk_endeks', 15, 2)->nullable();
            $table->decimal('ri_son_endeks', 15, 2)->nullable();
            $table->decimal('ri_fark_endeks', 15, 2)->nullable();
            $table->decimal('rc_ilk_endeks', 15, 2)->nullable();
            $table->decimal('rc_son_endeks', 15, 2)->nullable();
            $table->decimal('rc_fark_endeks', 15, 2)->nullable();
            
            // Tüketim alanları
            $table->decimal('t1_tuketim', 15, 2)->nullable();
            $table->decimal('t2_tuketim', 15, 2)->nullable();
            $table->decimal('t3_tuketim', 15, 2)->nullable();
            $table->decimal('trafo_kaybi_kwh', 15, 2)->nullable();
            $table->decimal('ek_tuketim', 15, 2)->nullable();
            
            // Lokasyon ve tarife bilgileri
            $table->string('ilce')->nullable();
            $table->decimal('yillik_tuketim', 15, 2)->nullable();
            $table->boolean('serbest_tuketici')->default(false);
            $table->decimal('fatura_edilecek_toplam_tuketim_kwh', 15, 2)->nullable();
            $table->string('tarife')->nullable();
            $table->string('tarife_2')->nullable();
            
            // Okumaüğ bilgileri
            $table->date('ilk_okuma')->nullable();
            $table->date('son_okuma')->nullable();
            
            // Fiyatlandırma alanları
            $table->decimal('birim_fiyat', 15, 4)->nullable();
            $table->decimal('dagitim_birim_fiyat', 15, 4)->nullable();
            $table->decimal('aktif_tuketim_tl', 15, 2)->nullable();
            $table->decimal('dagitim_bedeli', 15, 2)->nullable();
            $table->decimal('dagitim_bedeli_ek', 15, 2)->nullable();
            $table->decimal('enerji_fonu', 15, 2)->nullable();
            $table->decimal('reaktif_tl', 15, 2)->nullable();
            $table->decimal('acma_kapama_bedeli', 15, 2)->nullable();
            $table->decimal('gecikme_tutari', 15, 2)->nullable();
            $table->decimal('trt_fonu', 15, 2)->nullable();
            $table->decimal('btv', 15, 2)->nullable();
            
            // Fatura tutarı
            $table->decimal('fatura_tutari', 15, 2)->nullable();
            $table->decimal('fatura_tutari_ek', 15, 2)->nullable();
            $table->decimal('kdv', 15, 2)->nullable();
            $table->decimal('genel_toplam', 15, 2)->nullable();
            $table->decimal('btv_orani', 8, 4)->nullable();
            
            // Diğer alanlar
            $table->decimal('gunluk_ortalama_tuketim', 15, 2)->nullable();
            $table->string('baglanti_grubu')->nullable();
            $table->string('ilce_kodu')->nullable();
            $table->date('son_odeme_tarihi')->nullable();
            $table->decimal('tutar_toplam', 15, 2)->nullable();
            
            // İşlem bilgileri
            $table->string('current_row_hash')->nullable();
            $table->json('payload')->nullable();
            
            $table->foreignId('import_log_id')->nullable()->constrained('import_logs')->nullOnDelete();
            $table->foreignId('aktarim_yapan_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Status
            $table->enum('durum', ['yeni', 'islenme_bekle', 'islendi'])->default('yeni');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reaktifler');
    }
};
