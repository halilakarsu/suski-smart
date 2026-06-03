<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            if (!Schema::hasColumn('aboneler', 'prev_adres')) {
                $table->text('prev_adres')->nullable();
            }
            if (!Schema::hasColumn('aboneler', 'prev_sayac_seri_no')) {
                $table->string('prev_sayac_seri_no')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            $table->dropColumn(['prev_adres', 'prev_sayac_seri_no']);
        });
    }
};
