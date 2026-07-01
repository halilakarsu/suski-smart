<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tesis_ariza_kayitlari', function (Blueprint $table) {
            $table->string('durum', 50)->nullable()->default('Arıza Kaydı Yapıldı')->after('ariza_turu')->index();
        });
    }

    public function down(): void
    {
        Schema::table('tesis_ariza_kayitlari', function (Blueprint $table) {
            $table->dropColumn('durum');
        });
    }
};
