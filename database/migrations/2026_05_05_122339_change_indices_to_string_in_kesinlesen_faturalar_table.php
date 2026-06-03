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
        Schema::table('kesinlesen_faturalar', function (Blueprint $table) {
            $table->string('carpan')->nullable()->change();
            
            $table->string('t1_ilk_endeks')->nullable()->change();
            $table->string('t1_son_endeks')->nullable()->change();
            $table->string('t2_ilk_endeks')->nullable()->change();
            $table->string('t2_son_endeks')->nullable()->change();
            $table->string('t3_ilk_endeks')->nullable()->change();
            $table->string('t3_son_endeks')->nullable()->change();
            $table->string('t0_ilk_endeks')->nullable()->change();
            $table->string('to_son_endeks')->nullable()->change();
            
            $table->string('ri_ilk_endeks')->nullable()->change();
            $table->string('ri_son_endeks')->nullable()->change();
            $table->string('ri_fark_endeks')->nullable()->change();
            $table->string('rc_ilk_endeks')->nullable()->change();
            $table->string('rc_son_endeks')->nullable()->change();
            $table->string('rc_fark_endeks')->nullable()->change();
            
            $table->string('t1_tuketim')->nullable()->change();
            $table->string('t2_tuketim')->nullable()->change();
            $table->string('t3_tuketim')->nullable()->change();
            $table->string('trafo_kaybi_kwh')->nullable()->change();
            $table->string('ek_tuketim')->nullable()->change();
            $table->string('yillik_tuketim')->nullable()->after('ilce')->change();
            $table->string('fatura_edilecek_toplam_tuketim_kwh')->nullable()->change();
            $table->string('gunluk_ortalama_tuketim')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kesinlesen_faturalar', function (Blueprint $table) {
            // Revert logic would go here if needed
        });
    }
};
