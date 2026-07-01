<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tesis_araclar', function (Blueprint $table) {
            $table->id();
            $table->integer('sira_no')->nullable();
            $table->string('plaka', 50);
            $table->string('aracin_cinsi', 100);
            $table->string('arac_tipi', 100)->nullable();
            $table->string('kullanici_personel', 200)->nullable();
            $table->string('irtibat', 100)->nullable();
            $table->string('kullanildigi_is', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tesis_araclar');
    }
};
