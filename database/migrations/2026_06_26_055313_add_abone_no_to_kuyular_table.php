<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kuyular', function (Blueprint $table) {
            $table->string('abone_no', 100)->nullable()->after('kuyu_no')->index();
        });

        DB::statement('
            UPDATE kuyular k
            LEFT JOIN (
                SELECT kuyu_no, abone_no
                FROM tesis_ariza_kayitlari
                WHERE kuyu_no IS NOT NULL AND abone_no IS NOT NULL
                GROUP BY kuyu_no, abone_no
            ) a ON k.kuyu_no = a.kuyu_no
            SET k.abone_no = a.abone_no
            WHERE k.kuyu_no IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('kuyular', function (Blueprint $table) {
            $table->dropColumn('abone_no');
        });
    }
};
