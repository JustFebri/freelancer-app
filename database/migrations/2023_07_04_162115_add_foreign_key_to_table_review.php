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
            $table->foreign('order_id')
                ->references('order_id')
                ->on('order')
                ->onDelete('cascade');

            $table->foreign('client_id')
                ->references('client_id')
                ->on('client')
                ->onDelete('cascade');

            $table->foreign('freelancer_id')
                ->references('freelancer_id')
                ->on('freelancer')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('review', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['client_id']);
            $table->dropForeign(['freelancer_id']);
        });
    }
};
