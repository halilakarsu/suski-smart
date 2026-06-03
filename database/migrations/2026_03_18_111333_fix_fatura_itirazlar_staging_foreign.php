<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fatura_itirazlar', function (Blueprint $table) {
            $table->dropForeign(['staging_id']);
            $table->unsignedBigInteger('staging_id')->nullable()->change();
            $table->foreign('staging_id')
                ->references('id')
                ->on('fatura_staging')
                ->nullOnDelete();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    //
    }
};