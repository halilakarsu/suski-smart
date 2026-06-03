<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            $table->string('yerlesim_turu')->nullable()->after('ADRES')->comment('Köy veya boş (merkez)');
        });

        // Mevcut kayıtlar: köyü / koyu / koyü / köyu (büyük/küçük harf tüm varyasyonlar)
        DB::statement("
            UPDATE aboneler
            SET yerlesim_turu = 'Köy'
            WHERE ADRES LIKE '%köyü%' COLLATE utf8mb4_unicode_ci
               OR ADRES LIKE '%koyu%'  COLLATE utf8mb4_unicode_ci
               OR ADRES LIKE '%koyü%' COLLATE utf8mb4_unicode_ci
               OR ADRES LIKE '%köyu%' COLLATE utf8mb4_unicode_ci
               OR ADRES LIKE '%KÖYÜ%' COLLATE utf8mb4_unicode_ci
               OR ADRES LIKE '%KOYU%' COLLATE utf8mb4_unicode_ci
               OR ADRES LIKE '%KOYÜ%' COLLATE utf8mb4_unicode_ci
               OR ADRES LIKE '%KÖYU%' COLLATE utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            $table->dropColumn('yerlesim_turu');
        });
    }
};
