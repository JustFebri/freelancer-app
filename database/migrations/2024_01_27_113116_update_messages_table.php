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
        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('custom_id')->nullable();

            $table->foreign('service_id')
                ->references('service_id')
                ->on('service')
                ->cascadeOnDelete();

            $table->foreign('custom_id')
                ->references('custom_id')
                ->on('custom_orders')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['custom_id']);
            $table->dropColumn('service_id');
        });
    }
};
