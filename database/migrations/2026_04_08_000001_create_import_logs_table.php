<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('import_logs')) {
            Schema::create('import_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('dosya_adi');
                $table->string('orijinal_adi');
                $table->string('donem', 7)->comment('YYYY-MM formatı, örn: 2026-03');
                $table->string('disk')->default('local');
                $table->string('yol');
                $table->unsignedInteger('toplam_satir')->default(0);
                $table->unsignedInteger('islenen_satir')->default(0);
                $table->unsignedInteger('hata_sayisi')->default(0);
                $table->enum('durum', ['bekleniyor', 'isleniyor', 'tamamlandi', 'hata'])->default('bekleniyor');
                $table->text('notlar')->nullable();
                $table->json('sutun_eslestirme')->nullable()->comment('Excel başlığı → DB kolonu eşleştirmesi');
                $table->timestamp('isleme_basladi')->nullable();
                $table->timestamp('isleme_bitti')->nullable();
                $table->timestamps();

                $table->index(['donem', 'durum']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('import_logs');
    }
};
