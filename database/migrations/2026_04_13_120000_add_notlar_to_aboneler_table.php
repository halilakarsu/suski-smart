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
            $table->text('notlar')->nullable()->after('import_log_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            $table->dropColumn('notlar');
        });
    }
};
