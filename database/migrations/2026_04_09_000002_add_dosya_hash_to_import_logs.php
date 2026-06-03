<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_logs', function (Blueprint $table) {
            $table->string('dosya_hash', 64)->nullable()->after('donem')->comment('Aynı dosyanın tekrar yüklenmesini engellemek için SHA-256 özeti');
        });
    }

    public function down(): void
    {
        Schema::table('import_logs', function (Blueprint $table) {
            $table->dropColumn('dosya_hash');
        });
    }
};
