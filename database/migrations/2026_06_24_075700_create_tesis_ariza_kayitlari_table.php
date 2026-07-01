<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tesis_ariza_kayitlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abone_id')->nullable()->constrained('aboneler')->nullOnDelete();
            $table->integer('sira_no')->nullable();
            $table->string('kuyu_no', 50)->nullable();
            $table->string('tutanak_no', 50)->nullable();
            $table->string('ekip', 200)->nullable();
            $table->date('tarih')->nullable();
            $table->string('abone_no', 100)->nullable();
            $table->string('sayac_no', 100)->nullable();
            $table->string('ilce', 100)->nullable();
            $table->string('mahalle', 200);
            $table->string('sokak', 200)->nullable();
            $table->decimal('cbs_x', 20, 8)->nullable();
            $table->decimal('cbs_y', 20, 8)->nullable();
            $table->string('ariza_turu', 200);
            $table->timestamps();

            $table->index('ilce');
            $table->index('ariza_turu');
            $table->index('tarih');
            $table->index('abone_no');
            $table->index('kuyu_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tesis_ariza_kayitlari');
    }
};
