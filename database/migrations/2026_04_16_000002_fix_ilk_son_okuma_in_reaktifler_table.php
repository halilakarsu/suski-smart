<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Reaktifler tablosundaki ilk_okuma ve son_okuma'yı decimal'den date'e dönüştür
     */
    public function up(): void
    {
        // ilk_okuma ve son_okuma alanlarını date'e çevir
        DB::statement('ALTER TABLE `reaktifler` MODIFY `ilk_okuma` DATE NULL');
        DB::statement('ALTER TABLE `reaktifler` MODIFY `son_okuma` DATE NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `reaktifler` MODIFY `ilk_okuma` DECIMAL(15, 2) NULL');
        DB::statement('ALTER TABLE `reaktifler` MODIFY `son_okuma` DECIMAL(15, 2) NULL');
    }
};
