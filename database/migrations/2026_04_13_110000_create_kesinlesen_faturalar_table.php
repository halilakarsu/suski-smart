<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kesinlesen_faturalar', function (Blueprint $table) {
            $table->id();
            $table->string('tesisat_no')->index();
            $table->string('fatura_no')->index();
            $table->string('donem')->comment('Tahakkuk Dönemi, örn: 2026-03')->index();
            $table->date('son_odeme_tarihi')->nullable();
            
            $table->decimal('aktif_tuketim_bedeli', 15, 2)->default(0);
            $table->decimal('ceza_bedeli', 15, 2)->default(0);
            $table->decimal('kdv_tutari', 15, 2)->default(0);
            $table->decimal('toplam_odenecek_tutar', 15, 2)->default(0);
            
            $table->foreignId('aktarim_yapan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('import_log_id')->nullable()->constrained('import_logs')->nullOnDelete();
            
            $table->enum('odeme_durumu', ['bekliyor', 'odendi', 'iptal'])->default('bekliyor');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kesinlesen_faturalar');
    }
};
