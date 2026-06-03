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
        Schema::table('aboneler', function (Blueprint $table) {
            // Eksik alanlar
            if (!Schema::hasColumn('aboneler', 'UNVAN')) {
                $table->string('UNVAN')->nullable()->after('ABONE_TESIS_NO');
            }
            if (!Schema::hasColumn('aboneler', 'abone_grubu')) {
                $table->string('abone_grubu')->nullable()->after('carpan');
            }
            if (!Schema::hasColumn('aboneler', 'tarife')) {
                $table->string('tarife')->nullable()->after('abone_grubu');
            }
            if (!Schema::hasColumn('aboneler', 'tesis_cinsi')) {
                $table->string('tesis_cinsi')->nullable()->after('tarife');
            }
            
            // Eski değerleri tutmak için
            if (!Schema::hasColumn('aboneler', 'prev_abone_grubu')) {
                $table->string('prev_abone_grubu')->nullable()->after('prev_OG_durumu');
            }
            if (!Schema::hasColumn('aboneler', 'prev_tarife')) {
                $table->string('prev_tarife')->nullable()->after('prev_abone_grubu');
            }
            if (!Schema::hasColumn('aboneler', 'prev_tesis_cinsi')) {
                $table->string('prev_tesis_cinsi')->nullable()->after('prev_tarife');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            $table->dropColumnIfExists('UNVAN');
            $table->dropColumnIfExists('abone_grubu');
            $table->dropColumnIfExists('tarife');
            $table->dropColumnIfExists('tesis_cinsi');
            $table->dropColumnIfExists('prev_abone_grubu');
            $table->dropColumnIfExists('prev_tarife');
            $table->dropColumnIfExists('prev_tesis_cinsi');
        });
    }
};
