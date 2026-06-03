<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * BeklemeKontrolHavuzu tablosundaki ilk_okuma ve son_okuma'yı date'e dönüştür
     */
    public function up(): void
    {
        // ilk_okuma ve son_okuma'yı NULL'a set et, sonra date'e çevir
        DB::table('bekleme_kontrol_havuzu')->update([
            'ilk_okuma' => null,
            'son_okuma' => null,
        ]);
        
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `ilk_okuma` DATE NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `son_okuma` DATE NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `ilk_okuma` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `bekleme_kontrol_havuzu` MODIFY `son_okuma` VARCHAR(255) NULL');
    }
};
