<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staging_invoices', function (Blueprint $table) {
            $table->boolean('kontrol_edildi')->default(false)->after('kayit_durumu');
            $table->timestamp('kontrol_tarihi')->nullable()->after('kontrol_edildi');
        });
    }

    public function down(): void
    {
        Schema::table('staging_invoices', function (Blueprint $table) {
            $table->dropColumn(['kontrol_edildi', 'kontrol_tarihi']);
        });
    }
};
