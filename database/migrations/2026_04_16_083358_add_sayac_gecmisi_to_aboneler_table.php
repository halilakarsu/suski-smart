<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            if (!Schema::hasColumn('aboneler', 'sayac_gecmisi')) {
                $table->json('sayac_gecmisi')->nullable()->comment('Sayaç numarası değişim geçmişi')->after('SAYAC_SERI_NO');
            }
            if (!Schema::hasColumn('aboneler', 'sayac_guncelleme_tarihi')) {
                $table->timestamp('sayac_guncelleme_tarihi')->nullable()->comment('Son sayaç güncelleme tarihi')->after('sayac_gecmisi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            if (Schema::hasColumn('aboneler', 'sayac_gecmisi')) {
                $table->dropColumn('sayac_gecmisi');
            }
            if (Schema::hasColumn('aboneler', 'sayac_guncelleme_tarihi')) {
                $table->dropColumn('sayac_guncelleme_tarihi');
            }
        });
    }
};
