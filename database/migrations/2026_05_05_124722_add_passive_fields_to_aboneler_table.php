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
            $table->string('passive_reason')->nullable()->after('is_active');
            $table->date('last_invoice_date')->nullable()->after('passive_reason');
        });
    }

    public function down(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            $table->dropColumn(['passive_reason', 'last_invoice_date']);
        });
    }
};
