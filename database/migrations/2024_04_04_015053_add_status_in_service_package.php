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
        Schema::table('service_package', function (Blueprint $table) {
            $table->string('package_status')->default('active')->after('delivery_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_package', function (Blueprint $table) {
            $table->dropColumn('package_status');
        });
    }
};
