<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            if (!Schema::hasColumn('aboneler', 'BOLGE_KODU')) {
                $table->string('BOLGE_KODU')->nullable()->after('BOLGE_ADI');
            }
            // Bu abone sisteme yeni mi eklendi? (import sırasında ya da manuel)
            if (!Schema::hasColumn('aboneler', 'is_new')) {
                $table->boolean('is_new')->default(false);
            }
            // Kaynağı: 'seed', 'import', 'manual'
            if (!Schema::hasColumn('aboneler', 'created_via')) {
                $table->string('created_via')->nullable()->default('manual')->after('is_new');
            }
            // Hangi import logunda eklendi?
            if (!Schema::hasColumn('aboneler', 'import_log_id')) {
                $table->unsignedBigInteger('import_log_id')->nullable()->after('created_via');
            }
        });
    }

    public function down(): void
    {
        Schema::table('aboneler', function (Blueprint $table) {
            $table->dropColumn(['BOLGE_KODU', 'is_new', 'created_via', 'import_log_id']);
        });
    }
};
