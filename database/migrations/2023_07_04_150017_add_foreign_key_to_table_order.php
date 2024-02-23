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
        Schema::table('order', function (Blueprint $table) {
            $table->foreign('package_id')
                ->references('package_id')
                ->on('service_package')
                ->onDelete('cascade');

            $table->foreign('client_id')
                ->references('client_id')
                ->on('client')
                ->onDelete('cascade');

            $table->foreign('custom_id')
                ->references('custom_id')
                ->on('custom_orders')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropForeign(['client_id']);
            $table->dropForeign(['custom_id']);
        });
    }
};
