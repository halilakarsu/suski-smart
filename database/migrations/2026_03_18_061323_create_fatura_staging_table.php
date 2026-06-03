<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // fatura_staging
        Schema::create('fatura_staging', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ham_veri_id')->constrained('fatura_ham_veriler')->cascadeOnDelete();
            $table->json('veri');
            $table->string('row_hash', 64)->index();
            $table->string('donem', 7);
            $table->json('diff')->nullable();
            $table->enum('durum', ['bekliyor', 'onaylandi', 'itiraz_edildi'])->default('bekliyor');
            $table->string('itiraz_nedeni')->nullable();
            $table->foreignId('isleyen_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('isleme_tarihi')->nullable();

            $table->string('fatura_no')->nullable()->index();
            $table->string('tesisat')->index(); // ✅ unique kaldırıldı
            $table->string('unvan')->nullable();
            $table->decimal('fatura_tutar', 12, 2)->nullable();
            $table->decimal('toplam_tutar', 12, 2)->nullable();
            $table->date('tahakkuk_tarihi')->nullable();
            $table->date('son_odeme_tarihi')->nullable();
            $table->string('il')->nullable();
            $table->string('bolge_adi')->nullable();

            $table->timestamps();

            // ✅ Staging'de de aynı kısıt
            $table->unique(['fatura_no', 'tesisat', 'donem'], 'staging_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatura_staging');
    }
};