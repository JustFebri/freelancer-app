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
        Schema::table('interest', function (Blueprint $table) {
            $table->foreign('client_id')
                ->references('client_id')
                ->on('client')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('category_id')
                ->on('category')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interest', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['category_id']);
        });
    }
};
