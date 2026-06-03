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
            if (!Schema::hasColumn('kesinlesen_faturalar', 'odeme_durumu')) {
                $table->string('odeme_durumu')->default('bekliyor')->after('donem');
            }
            if (!Schema::hasColumn('kesinlesen_faturalar', 'odeme_tarihi')) {
                $table->dateTime('odeme_tarihi')->nullable()->after('odeme_durumu');
            }
            if (!Schema::hasColumn('kesinlesen_faturalar', 'odeme_yapan_id')) {
                $table->unsignedBigInteger('odeme_yapan_id')->nullable()->after('odeme_tarihi');
            }
            if (!Schema::hasColumn('kesinlesen_faturalar', 'aktarim_yapan_id')) {
                $table->unsignedBigInteger('aktarim_yapan_id')->nullable()->after('odeme_yapan_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kesinlesen_faturalar', function (Blueprint $table) {
            //
        });
    }
};
