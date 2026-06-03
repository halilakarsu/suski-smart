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
        Schema::create('sabit_kolon_eslestirmeleri', function (Blueprint $table) {
            $table->id();
            $table->string('excel_baslik')->unique(); // 'Tesisat', 'FATURA NO'
            $table->string('sistem_alani');   // 'tesisat_no', 'fatura_no'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sabit_kolon_eslestirmeleri');
    }
};
