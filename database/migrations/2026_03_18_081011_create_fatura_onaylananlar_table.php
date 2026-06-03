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
        // fatura_onaylananlar
        Schema::create('fatura_onaylananlar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staging_id')->constrained('fatura_staging')->cascadeOnDelete();
            $table->foreignId('ham_veri_id')->constrained('fatura_ham_veriler');
            $table->string('donem', 7)->index();
            $table->string('fatura_no')->nullable()->index();
            $table->string('tesisat')->index();
            $table->string('unvan')->nullable();
            $table->decimal('fatura_tutar', 12, 2)->nullable();
            $table->decimal('toplam_tutar', 12, 2)->nullable();
            $table->date('tahakkuk_tarihi')->nullable();
            $table->date('son_odeme_tarihi')->nullable();
            $table->string('il')->nullable();
            $table->string('bolge_adi')->nullable();
            $table->foreignId('onaylayan_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('onay_tarihi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatura_onaylananlar');
    }
};