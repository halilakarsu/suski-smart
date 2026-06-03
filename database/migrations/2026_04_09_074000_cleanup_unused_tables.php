<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Sadece Domain/Eski proje tablolarını siliyoruz (Kafa karıştırıcı olanlar)
        Schema::dropIfExists('fatura_import_staging');
        Schema::dropIfExists('itiraz_edilenler');
        Schema::dropIfExists('itirazlar');
        Schema::dropIfExists('odeme_gonderilenler');
        Schema::dropIfExists('sabit_kolon_eslestirmeleri');
        Schema::dropIfExists('fatura_ham_veriler');
        Schema::dropIfExists('fatura_staging');

        // NOT: cache, sessions, jobs gibi tablolar Laravel'in "motor" kısmıdır.
        // Sistemin tam performanslı çalışması için bunları muhafaza ediyoruz.

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
