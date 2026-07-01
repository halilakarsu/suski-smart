<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tesis_ariza_kayitlari', function (Blueprint $table) {
            $table->integer('sira_no')->nullable()->change();
            $table->string('kuyu_no', 50)->nullable()->change();
            $table->string('tutanak_no', 50)->nullable()->change();
            $table->string('ekip', 200)->nullable()->change();
            $table->date('tarih')->nullable()->change();
            $table->string('abone_no', 100)->nullable()->change();
            $table->string('sayac_no', 100)->nullable()->change();
            $table->string('ilce', 100)->nullable()->change();
            $table->string('mahalle', 200)->nullable()->change();
            $table->string('sokak', 200)->nullable()->change();
            $table->decimal('cbs_x', 20, 8)->nullable()->change();
            $table->decimal('cbs_y', 20, 8)->nullable()->change();
            $table->string('ariza_turu', 200)->nullable()->change();
        });
    }

    public function down(): void
    {
        // 无法回滚
    }
};
