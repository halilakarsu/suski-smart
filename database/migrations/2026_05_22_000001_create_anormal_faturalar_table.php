<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anormal_faturalar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kesinlesen_fatura_id')->unique()->constrained('kesinlesen_faturalar')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('durum', ['kaydedildi', 'gormezden_gelindi'])->default('kaydedildi')->index();
            $table->text('islem_notu')->nullable();

            $table->string('tesisat_no')->nullable()->index();
            $table->string('abone_tesis_no')->nullable()->index();
            $table->string('fatura_no')->nullable()->index();
            $table->string('hesap_adi')->nullable();
            $table->string('donem')->nullable()->index();
            $table->string('ilce')->nullable()->index();
            $table->string('ilce_kodu')->nullable()->index();
            $table->string('baglanti_grubu')->nullable()->index();
            $table->string('yerlesim_turu')->nullable()->index();
            $table->string('tarife')->nullable()->index();

            $table->decimal('fatura_edilecek_toplam_tuketim_kwh', 20, 6)->nullable();
            $table->decimal('tutar_toplam', 20, 2)->nullable();
            $table->date('ilk_okuma')->nullable();
            $table->date('son_okuma')->nullable();
            $table->json('anomali_payload')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anormal_faturalar');
    }
};
