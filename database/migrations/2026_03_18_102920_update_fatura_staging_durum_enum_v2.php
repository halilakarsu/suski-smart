<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Önce enum'u TÜM değerleri kapsayacak şekilde genişlet
        DB::statement("
        ALTER TABLE fatura_staging 
        MODIFY COLUMN durum ENUM('bekliyor','kontrol_edildi','itiraz_edildi','inceleniyor','onaylandi') 
        NOT NULL DEFAULT 'bekliyor'
    ");

        // 2. Eski verileri yeni değerlere güncelle
        DB::statement("UPDATE fatura_staging SET durum = 'inceleniyor' WHERE durum = 'bekliyor'");
        DB::statement("UPDATE fatura_staging SET durum = 'onaylandi' WHERE durum = 'kontrol_edildi'");

        // 3. Eski değerleri kaldır, sadece yenileri bırak
        DB::statement("
        ALTER TABLE fatura_staging 
        MODIFY COLUMN durum ENUM('inceleniyor','onaylandi','itiraz_edildi') 
        NOT NULL DEFAULT 'inceleniyor'
    ");
    }

    public function down(): void
    {
        DB::statement("
        ALTER TABLE fatura_staging sa
        MODIFY COLUMN durum ENUM('bekliyor','kontrol_edildi','itiraz_edildi') 
        NOT NULL DEFAULT 'bekliyor'
    ");
    }
};