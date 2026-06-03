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
        Schema::table('bekleme_kontrol_havuzu', function (Blueprint $table) {
            if (!Schema::hasColumn('bekleme_kontrol_havuzu', 'dagitim_bedeli')) {
                $table->string('dagitim_bedeli')->nullable()->after('aktif_tuketim_tl');
            }
            if (!Schema::hasColumn('bekleme_kontrol_havuzu', 'fatura_tutari')) {
                $table->string('fatura_tutari')->nullable()->after('btv');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bekleme_kontrol_havuzu', function (Blueprint $table) {
            $table->dropColumn(['dagitim_bedeli', 'fatura_tutari']);
        });
    }
};
