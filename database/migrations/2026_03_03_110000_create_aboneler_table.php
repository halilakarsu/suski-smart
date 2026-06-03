<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('aboneler', function (Blueprint $table) {
            $table->id();
            $table->string('ABONE_TESIS_NO')->unique()->index();
            $table->string('BOLGE_ADI')->nullable();
            $table->string('ADRES')->nullable();
            $table->string('SAYAC_SERI_NO')->nullable();
            $table->string('KUL_NO')->nullable();
            $table->string('SERBEST_TUKETICI_EH')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aboneler');
    }
};
