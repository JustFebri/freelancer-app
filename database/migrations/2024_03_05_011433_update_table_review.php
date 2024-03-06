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
        Schema::table('review', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
            $table->string('comment')->nullable()->change();
            $table->unsignedBigInteger('service_id');
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
        Schema::table('review', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
            $table->string('comment')->nullable(false)->change();
            
            
            $table->string('order_id');
            $table->foreign('order_id')
                ->references('order_id')
                ->on('order')
                ->onDelete('cascade');
        });
    }
};
