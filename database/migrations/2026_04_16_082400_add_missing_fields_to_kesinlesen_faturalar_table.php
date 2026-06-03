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
        Schema::table('kesinlesen_faturalar', function (Blueprint $table) {
            if (!Schema::hasColumn('kesinlesen_faturalar', 'abone_tesis_no')) {
                $table->string('abone_tesis_no')->nullable()->after('tesisat_no')->index();
            }
            if (!Schema::hasColumn('kesinlesen_faturalar', 'ilce')) {
                $table->string('ilce')->nullable()->after('abone_tesis_no');
            }
            if (!Schema::hasColumn('kesinlesen_faturalar', 'adres')) {
                $table->text('adres')->nullable()->after('ilce');
            }
            if (!Schema::hasColumn('kesinlesen_faturalar', 'fatura_edilecek_toplam_tuketim_kwh')) {
                $table->decimal('fatura_edilecek_toplam_tuketim_kwh', 15, 2)->default(0)->after('toplam_odenecek_tutar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kesinlesen_faturalar', function (Blueprint $table) {
            if (Schema::hasColumn('kesinlesen_faturalar', 'abone_tesis_no')) {
                $table->dropColumn('abone_tesis_no');
            }
            if (Schema::hasColumn('kesinlesen_faturalar', 'ilce')) {
                $table->dropColumn('ilce');
            }
            if (Schema::hasColumn('kesinlesen_faturalar', 'adres')) {
                $table->dropColumn('adres');
            }
            if (Schema::hasColumn('kesinlesen_faturalar', 'fatura_edilecek_toplam_tuketim_kwh')) {
                $table->dropColumn('fatura_edilecek_toplam_tuketim_kwh');
            }
        });
    }
};
