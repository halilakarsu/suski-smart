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
            if (!Schema::hasColumn('bekleme_kontrol_havuzu', 'payload')) {
                $table->json('payload')->nullable()->after('current_row_hash');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bekleme_kontrol_havuzu', function (Blueprint $table) {
            $table->dropColumn('payload');
        });
    }
};
