<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // bekleme_kontrol_havuzu
        if (Schema::hasColumn('bekleme_kontrol_havuzu', 'to_son_endeks')) {
            Schema::table('bekleme_kontrol_havuzu', function (Blueprint $table) {
                $table->renameColumn('to_son_endeks', 't0_son_endeks');
            });
        }

        // kesinlesen_faturalar
        if (Schema::hasColumn('kesinlesen_faturalar', 'to_son_endeks')) {
            Schema::table('kesinlesen_faturalar', function (Blueprint $table) {
                $table->renameColumn('to_son_endeks', 't0_son_endeks');
            });
        }

        // reaktifler
        if (Schema::hasColumn('reaktifler', 'to_son_endeks')) {
            Schema::table('reaktifler', function (Blueprint $table) {
                $table->renameColumn('to_son_endeks', 't0_son_endeks');
            });
        }

        // itiraz_edilenler
        if (Schema::hasColumn('itiraz_edilenler', 'to_son_endeks')) {
            Schema::table('itiraz_edilenler', function (Blueprint $table) {
                $table->renameColumn('to_son_endeks', 't0_son_endeks');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bekleme_kontrol_havuzu', 't0_son_endeks')) {
            Schema::table('bekleme_kontrol_havuzu', function (Blueprint $table) {
                $table->renameColumn('t0_son_endeks', 'to_son_endeks');
            });
        }

        if (Schema::hasColumn('kesinlesen_faturalar', 't0_son_endeks')) {
            Schema::table('kesinlesen_faturalar', function (Blueprint $table) {
                $table->renameColumn('t0_son_endeks', 'to_son_endeks');
            });
        }

        if (Schema::hasColumn('reaktifler', 't0_son_endeks')) {
            Schema::table('reaktifler', function (Blueprint $table) {
                $table->renameColumn('t0_son_endeks', 'to_son_endeks');
            });
        }

        if (Schema::hasColumn('itiraz_edilenler', 't0_son_endeks')) {
            Schema::table('itiraz_edilenler', function (Blueprint $table) {
                $table->renameColumn('t0_son_endeks', 'to_son_endeks');
            });
        }
    }
};
