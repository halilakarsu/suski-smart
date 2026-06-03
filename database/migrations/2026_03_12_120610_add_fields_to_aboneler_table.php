<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tüm alanlar artık create_aboneler_table migration'ında tanımlı.
     * Bu migration geriye uyumluluk için boş bırakıldı.
     */
    public function up(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            if (!Schema::hasColumn('aboneler', 'SAYAC_SERI_NO')) {
                $table->string('SAYAC_SERI_NO')->nullable()->after('ABONE_TESIS_NO');
            }
            if (!Schema::hasColumn('aboneler', 'KUL_NO')) {
                $table->string('KUL_NO')->nullable()->after('SAYAC_SERI_NO');
            }
            if (!Schema::hasColumn('aboneler', 'SERBEST_TUKETICI_EH')) {
                $table->string('SERBEST_TUKETICI_EH')->nullable()->after('ADRES');
            }
        });
    }

    public function down(): void
    {
        // Hiçbir şey yapma — alanlar base migration'da yönetiliyor
    }
};