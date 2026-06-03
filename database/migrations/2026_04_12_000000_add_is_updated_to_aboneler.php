<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            if (!Schema::hasColumn('aboneler', 'is_updated')) {
                $table->boolean('is_updated')->default(false)->after('is_new');
            }
        });
    }

    public function down(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            $table->dropColumn('is_updated');
        });
    }
};
