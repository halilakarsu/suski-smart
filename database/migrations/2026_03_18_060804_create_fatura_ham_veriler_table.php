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
        // fatura_ham_veriler
        Schema::create('fatura_ham_veriler', function (Blueprint $table) {
            $table->id();
            $table->json('veri');
            $table->string('row_hash', 64)->index();
            $table->string('donem', 7);
            $table->string('kaynak')->nullable();
            $table->unsignedInteger('satir_no')->nullable();

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

            // ✅ Gerçek unique kısıt: 3'lü birlikte
            $table->unique(['fatura_no', 'tesisat', 'donem'], 'ham_veri_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatura_ham_veriler');
    }
};