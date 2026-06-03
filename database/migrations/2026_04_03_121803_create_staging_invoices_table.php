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
        Schema::create('staging_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_invoice_data_id')->constrained('raw_invoice_data')->cascadeOnDelete(); 
            $table->foreignId('import_log_id')->constrained()->cascadeOnDelete();
            
            // İş kuralları sonucu belirlenen durum
            $table->enum('kayit_durumu', ['yeni', 'mukerrer', 'degisti'])->index();
            
            // Temizlenmiş ve tip dönüşümü yapılmış alanlar
            $table->string('fatura_no', 50)->index();
            $table->string('tesisat_no', 50)->nullable()->index();
            $table->date('son_odeme_tarihi')->nullable();
            $table->decimal('tutar_toplam', 15, 2)->nullable();
            
            // Sistemdeki güncel hash (İlerideki importlar bu hash ile karşılaştırılacak)
            $table->char('current_row_hash', 32)->index();
            
            $table->timestamps();

            // Bir fatura numarası staging tablosunda sadece 1 kez tekil olarak bulunabilir
            $table->unique('fatura_no'); 
        }); 
    }

    public function down(): void
    {
        Schema::dropIfExists('staging_invoices');
    }
};
