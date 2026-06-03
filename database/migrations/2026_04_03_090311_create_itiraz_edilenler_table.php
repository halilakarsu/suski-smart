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
        Schema::create('itiraz_edilenler', function (Blueprint $table) {
            $table->id();
            // staging silinse bile itiraz kaydı dursun
            $table->unsignedBigInteger('staging_id');
            $table->foreign('staging_id')->references('id')->on('fatura_import_staging')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('itiraz_notu')->nullable();
            $table->enum('durum', ['bekliyor', 'kabul_edildi', 'reddedildi'])->default('bekliyor');
            $table->text('sonuc_notu')->nullable(); // itiraz sonuçlandığında
            $table->foreignId('sonuclayan_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sonuclanma_tarihi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itiraz_edilenler');
    }
};
