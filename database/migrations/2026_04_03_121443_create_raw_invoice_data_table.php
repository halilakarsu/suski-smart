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
        Schema::create('raw_invoice_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_log_id')->constrained()->cascadeOnDelete();
            
            // Arama ve eşleştirme yapacağımız kilit alanlar
            $table->string('fatura_no', 50)->nullable()->index(); 
            
            // Verinin md5 özeti (Karşılaştırma için can damarımız)
            $table->char('row_hash', 32)->index(); 
            
            // Satırın orijinal tüm içeriği
            $table->json('payload'); 
            
            $table->timestamps();
            
            // Aynı dosya içinde bile birebir aynı satır varsa mükerrer kaydı engellemek için
            $table->unique(['import_log_id', 'row_hash']); 
        }); 
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_invoice_data');
    }
};
