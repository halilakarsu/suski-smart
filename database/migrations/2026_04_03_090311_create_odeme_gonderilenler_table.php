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
        Schema::create('odeme_gonderilenler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_log_id')->constrained('import_logs')->cascadeOnDelete();
            $table->foreignId('tesis_id')->nullable()->constrained('aboneler')->nullOnDelete();
            $table->string('tesisat_no', 50)->nullable()->index();
            $table->string('fatura_no', 50)->nullable()->index();
            $table->string('donem', 20)->nullable();
            $table->string('unvan', 255)->nullable();
            $table->string('ilce', 100)->nullable();
            $table->decimal('tutar_toplam', 14, 2)->nullable();
            $table->decimal('borc_toplam', 14, 2)->nullable();
            $table->date('fatura_tarihi')->nullable();
            $table->date('son_odeme_tarihi')->nullable();
            $table->foreignId('gonderen_user_id')->constrained('users');
            $table->timestamp('gonderim_tarihi');
            $table->json('ham_veri')->nullable(); // tüm ham sütunlar başlık adıyla
            $table->timestamps();
        
            // Aynı fatura iki kez ödemeye gönderilmesin
            $table->unique(['fatura_no', 'donem'], 'unique_odeme_fatura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odeme_gonderilenler');
    }
};
