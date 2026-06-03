<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sync Reaktifler
        Schema::table('reaktifler', function (Blueprint $table) {
            if (!Schema::hasColumn('reaktifler', 'donem')) {
                $table->string('donem')->nullable()->after('fatura_no');
            }
            if (!Schema::hasColumn('reaktifler', 'abone_tesis_no')) {
                $table->string('abone_tesis_no')->nullable()->after('tesisat_no')->index();
            }
            if (!Schema::hasColumn('reaktifler', 'hesap_adi')) {
                $table->string('hesap_adi')->nullable()->after('tesisat_no');
            }
            if (!Schema::hasColumn('reaktifler', 'hamveri_id')) {
                $table->unsignedBigInteger('hamveri_id')->nullable()->after('import_log_id');
            }
            if (!Schema::hasColumn('reaktifler', 'itiraz_edildi')) {
                $table->boolean('itiraz_edildi')->default(false);
            }
            if (!Schema::hasColumn('reaktifler', 'itiraz_aciklamasi')) {
                $table->text('itiraz_aciklamasi')->nullable();
            }
            // Ensure types match BeklemeKontrolHavuzu (mostly decimal changes if any)
        });

        // 2. Sync Itiraz Edilenler (Making it a full matching table)
        if (!Schema::hasTable('itiraz_edilenler')) {
            Schema::create('itiraz_edilenler', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('tesisat_no')->nullable()->index();
                $table->string('hesap_adi')->nullable();
                $table->string('abone_tesis_no')->nullable()->index();
                $table->string('fatura_no')->nullable()->index();
                $table->string('donem')->nullable();
                $table->foreignId('import_log_id')->nullable()->constrained('import_logs')->nullOnDelete();
                
                $table->string('sira_no')->nullable();
                $table->string('pmum_id')->nullable();
                $table->string('sayac_seri_no')->nullable();
                $table->decimal('carpan', 15, 2)->nullable();
                $table->text('adres')->nullable();
                $table->string('ilce')->nullable();
                $table->string('dagitim')->nullable();
                
                // Endekses
                $table->decimal('t1_ilk_endeks', 15, 2)->default(0);
                $table->decimal('t1_son_endeks', 15, 2)->default(0);
                $table->decimal('t2_ilk_endeks', 15, 2)->default(0);
                $table->decimal('t2_son_endeks', 15, 2)->default(0);
                $table->decimal('t3_ilk_endeks', 15, 2)->default(0);
                $table->decimal('t3_son_endeks', 15, 2)->default(0);
                $table->decimal('t0_ilk_endeks', 15, 2)->default(0);
                $table->decimal('to_son_endeks', 15, 2)->default(0);
                
                $table->decimal('ri_ilk_endeks', 15, 2)->default(0);
                $table->decimal('ri_son_endeks', 15, 2)->default(0);
                $table->decimal('ri_fark_endeks', 15, 2)->default(0);
                $table->decimal('rc_ilk_endeks', 15, 2)->default(0);
                $table->decimal('rc_son_endeks', 15, 2)->default(0);
                $table->decimal('rc_fark_endeks', 15, 2)->default(0);
                
                $table->decimal('t1_tuketim', 15, 2)->default(0);
                $table->decimal('t2_tuketim', 15, 2)->default(0);
                $table->decimal('t3_tuketim', 15, 2)->default(0);
                $table->decimal('trafo_kaybi_kwh', 15, 2)->default(0);
                $table->decimal('ek_tuketim', 15, 2)->default(0);
                $table->decimal('yillik_tuketim', 15, 2)->default(0);
                $table->decimal('fatura_edilecek_toplam_tuketim_kwh', 15, 2)->default(0);
                
                $table->string('tarife')->nullable();
                $table->string('tarife_2')->nullable();
                $table->date('ilk_okuma')->nullable();
                $table->date('son_okuma')->nullable();
                $table->date('son_odeme_tarihi')->nullable();
                
                $table->decimal('birim_fiyat', 15, 5)->default(0);
                $table->decimal('dagitim_birim_fiyat', 15, 5)->default(0);
                $table->decimal('aktif_tuketim_tl', 15, 2)->default(0);
                $table->decimal('dagitim_bedeli', 15, 2)->default(0);
                $table->decimal('reaktif_tl', 15, 2)->default(0);
                $table->decimal('kdv', 15, 2)->default(0);
                $table->decimal('genel_toplam', 15, 2)->default(0);
                
                $table->json('payload')->nullable();
                $table->boolean('itiraz_edildi')->default(true);
                $table->text('itiraz_aciklamasi')->nullable();
                $table->enum('durum', ['bekliyor', 'kabul_edildi', 'reddedildi'])->default('bekliyor');
                $table->text('sonuc_notu')->nullable();
                $table->foreignId('sonuclayan_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('sonuclanma_tarihi')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
    }
};
