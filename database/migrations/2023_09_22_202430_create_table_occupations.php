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
        Schema::create('occupation', function (Blueprint $table) {
            $table->id('occupation_id');
            $table->unsignedBigInteger('freelancer_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->year('from')->nullable();
            $table->year('to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupation');
    }
};
