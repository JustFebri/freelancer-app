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
            $table->foreign('service_id')
                ->references('service_id')
                ->on('service')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_package', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
        });
    }
};
