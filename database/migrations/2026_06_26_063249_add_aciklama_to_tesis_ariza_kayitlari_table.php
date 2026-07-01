<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tesis_ariza_kayitlari', function (Blueprint $table) {
            $table->text('aciklama')->nullable()->after('durum')->comment('Arıza açıklama / notlar');
        });
    }

    public function down(): void
    {
        Schema::table('tesis_ariza_kayitlari', function (Blueprint $table) {
            $table->dropColumn('aciklama');
        });
    }
};
