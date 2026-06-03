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
        Schema::table('sabit_kolon_eslestirmeleri', function (Blueprint $table) {
            $table->string('aciklama', 255)->nullable()->after('sistem_alani');   // kullanıcı notu
            $table->boolean('aktif')->default(true)->after('aciklama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sabit_kolon_eslestirmeleri', function (Blueprint $table) {
            //
        });
    }
};
