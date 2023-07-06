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
        Schema::table('portfolio_img', function (Blueprint $table) {
            $table->foreign('portfolio_id')
                ->references('portfolio_id')
                ->on('portfolio')
                ->onDelete('cascade');

            $table->foreign('picture_id')
                ->references('picture_id')
                ->on('picture')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolio_img', function (Blueprint $table) {
            $table->dropForeign(['portfolio_id']);
            $table->dropForeign(['picture_id']);
        });
    }
};
