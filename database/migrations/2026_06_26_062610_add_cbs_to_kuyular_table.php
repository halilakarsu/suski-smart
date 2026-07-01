<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('kuyular', 'cbs_x')) {
            Schema::table('kuyular', function (Blueprint $table) {
                $table->decimal('cbs_x', 20, 8)->nullable()->after('adres')->comment('CBS X Koordinatı');
                $table->decimal('cbs_y', 20, 8)->nullable()->after('cbs_x')->comment('CBS Y Koordinatı');
            });
        } else {
            Schema::table('kuyular', function (Blueprint $table) {
                $table->decimal('cbs_x', 20, 8)->nullable()->change();
                $table->decimal('cbs_y', 20, 8)->nullable()->change();
            });
        }

        DB::statement('
            UPDATE kuyular k
            INNER JOIN (
                SELECT kuyu_no, cbs_x, cbs_y
                FROM tesis_ariza_kayitlari
                WHERE kuyu_no IS NOT NULL AND cbs_x IS NOT NULL AND cbs_y IS NOT NULL
                GROUP BY kuyu_no, cbs_x, cbs_y
            ) a ON k.kuyu_no = a.kuyu_no
            SET k.cbs_x = a.cbs_x, k.cbs_y = a.cbs_y
            WHERE k.kuyu_no IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('kuyular', function (Blueprint $table) {
            $table->dropColumn(['cbs_x', 'cbs_y']);
        });
    }
};
