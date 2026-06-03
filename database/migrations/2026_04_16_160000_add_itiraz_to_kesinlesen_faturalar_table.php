<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kesinlesen_faturalar', function (Blueprint $table) {
            $table->boolean('itiraz_edildi')->default(false)->after('odeme_durumu');
            $table->text('itiraz_aciklamasi')->nullable()->after('itiraz_edildi');
        });
    }

    public function down(): void
    {
        Schema::table('kesinlesen_faturalar', function (Blueprint $table) {
            $table->dropColumn(['itiraz_edildi', 'itiraz_aciklamasi']);
        });
    }
};
