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
        Schema::table('aboneler', function (Blueprint $table) {
            // Eksik bilgi alanları
            if (!Schema::hasColumn('aboneler', 'PMUM')) {
                $table->string('PMUM')->nullable()->comment('PMUM numarası');
            }
            if (!Schema::hasColumn('aboneler', 'hesap_adi')) {
                $table->string('hesap_adi')->nullable()->comment('Tarife/Hesap adı (TARIFE)');
            }
            if (!Schema::hasColumn('aboneler', 'baglanti_grubu')) {
                $table->string('baglanti_grubu')->nullable()->comment('Bağlantı türü/Tesis cinsi (TESIS_CINSI)');
            }
            if (!Schema::hasColumn('aboneler', 'OG_durumu')) {
                $table->boolean('OG_durumu')->default(0)->comment('0 = AG, 1 = OG (SOZLESME_GUCU)');
            }
            if (!Schema::hasColumn('aboneler', 'dagitim_merkezi')) {
                $table->string('dagitim_merkezi')->nullable()->comment('Dağıtım merkezi (DAGITIM)');
            }
            if (!Schema::hasColumn('aboneler', 'carpan')) {
                $table->decimal('carpan', 8, 4)->default(1)->comment('Çarpan');
            }
            
            // Eski bilgileri takip etmek için
            if (!Schema::hasColumn('aboneler', 'prev_hesap_adi')) {
                $table->string('prev_hesap_adi')->nullable()->comment('Eski hesap adı');
            }
            if (!Schema::hasColumn('aboneler', 'prev_baglanti_grubu')) {
                $table->string('prev_baglanti_grubu')->nullable()->comment('Eski bağlantı türü');
            }
            if (!Schema::hasColumn('aboneler', 'prev_OG_durumu')) {
                $table->boolean('prev_OG_durumu')->nullable()->comment('Eski OG durumu');
            }
            
            // Tarih takibi
            if (!Schema::hasColumn('aboneler', 'guncelleme_tarihi')) {
                $table->timestamp('guncelleme_tarihi')->nullable()->comment('Son güncelleme tarihi');
            }
            if (!Schema::hasColumn('aboneler', 'guncelleme_detay')) {
                $table->json('guncelleme_detay')->nullable()->comment('Güncelleme geçmişi (JSON)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            $columns = [
                'PMUM', 'hesap_adi', 'baglanti_grubu', 'OG_durumu', 'dagitim_merkezi', 'carpan',
                'prev_hesap_adi', 'prev_baglanti_grubu', 'prev_OG_durumu',
                'guncelleme_tarihi', 'guncelleme_detay'
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('aboneler', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
