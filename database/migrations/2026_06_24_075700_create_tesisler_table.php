<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tesisler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abone_id')->nullable()->constrained('aboneler')->nullOnDelete();
            $table->integer('sira_no')->nullable();
            $table->enum('durum', ['aktif', 'pasif'])->default('aktif');
            $table->string('ilce', 100);
            $table->string('mahalle', 200);
            $table->string('sokak', 200)->nullable();
            $table->string('kuyu_no', 50)->nullable();
            $table->decimal('cbs_x', 20, 8)->nullable();
            $table->decimal('cbs_y', 20, 8)->nullable();
            $table->date('tesis_kurulma_tarihi')->nullable();
            $table->date('hibe_tarihi')->nullable();
            $table->string('abone_tipi', 20)->nullable();
            $table->date('abone_tarihi')->nullable();
            $table->string('sayac_no', 100)->nullable();
            $table->string('abone_no', 100)->nullable();
            $table->string('abone_iptali_yazildi_mi', 100)->nullable();
            $table->string('abone_iptal_edildi_mi', 100)->nullable();
            $table->string('kacak_elektrik_kullanimi_var_mi', 50)->nullable();
            $table->string('kacak_borcu_var_mi', 50)->nullable();
            $table->decimal('toplam_fatura_tutari', 20, 2)->nullable();
            $table->string('trafo_gucu', 50)->nullable();
            $table->string('trafo_seri_no', 100)->nullable();
            $table->string('trafo_cbs', 100)->nullable();
            $table->string('enh_durumu', 100)->nullable();
            $table->string('kesif_durumu', 100)->nullable();
            $table->date('demontaj_tarihi')->nullable();
            $table->text('demontaj_yapilan_malzemeler')->nullable();
            $table->decimal('gelir', 20, 2)->nullable();
            $table->decimal('gider', 20, 2)->nullable();
            $table->timestamps();

            $table->index('ilce');
            $table->index('durum');
            $table->index('kuyu_no');
            $table->index('abone_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tesisler');
    }
};
