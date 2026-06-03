<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE fatura_staging 
            MODIFY COLUMN durum ENUM('bekliyor','kontrol_edildi','itiraz_edildi') 
            NOT NULL DEFAULT 'bekliyor'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE fatura_staging 
            MODIFY COLUMN durum ENUM('bekliyor','onaylandi','itiraz_edildi') 
            NOT NULL DEFAULT 'bekliyor'
        ");
    }
};