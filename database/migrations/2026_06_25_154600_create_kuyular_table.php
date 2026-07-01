<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kuyular', function (Blueprint $table) {
            $table->id();

            // ── Temel Kimlik ──────────────────────────────────────────────
            $table->string('kuyu_no', 50)->nullable()->comment('Kuyu Numarası');

            // ── Konum ─────────────────────────────────────────────────────
            $table->string('ilce', 100)->nullable();
            $table->string('adres', 500)->nullable();

            // ── Teknik Bilgiler ───────────────────────────────────────────
            $table->decimal('demontaj_derinligi', 10, 2)->nullable()->comment('Demontaj Derinliği (m)');
            $table->decimal('montaj_derinligi',   10, 2)->nullable()->comment('Montaj Derinliği (m)');
            $table->string('depo_bilgisi', 300)->nullable();
            $table->string('boru_tipi', 200)->nullable();
            $table->string('kablo', 200)->nullable();
            $table->string('motor', 300)->nullable();
            $table->string('pompa', 300)->nullable();
            $table->string('debi', 100)->nullable();

            // ── Notlar & Durum ────────────────────────────────────────────
            $table->text('aciklama')->nullable();
            $table->enum('durum', ['aktif', 'pasif'])->default('aktif');

            // ── Tarihler ──────────────────────────────────────────────────
            $table->timestamp('olusturulma_tarihi')->nullable();
            $table->timestamp('guncellenme_tarihi')->nullable();

            $table->timestamps();

            $table->index('durum');
            $table->index('ilce');
            $table->index('kuyu_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuyular');
    }
};
