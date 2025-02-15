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
        Schema::create('portfolio_img', function (Blueprint $table) {
            $table->id('portfolioImg_id');
            $table->unsignedBigInteger('portfolio_id');
            $table->unsignedBigInteger('picture_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_img');
    }
};
