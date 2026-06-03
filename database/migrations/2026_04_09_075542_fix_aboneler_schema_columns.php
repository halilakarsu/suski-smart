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
            if (!Schema::hasColumn('aboneler', 'UNVAN')) {
                $table->string('UNVAN')->nullable()->after('ABONE_TESIS_NO');
            }
            if (!Schema::hasColumn('aboneler', 'SERBEST_TUKETICI_EH')) {
                $table->string('SERBEST_TUKETICI_EH')->nullable()->after('KUL_NO');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            $table->dropColumn(['UNVAN', 'SERBEST_TUKETICI_EH']);
        });
    }
};
