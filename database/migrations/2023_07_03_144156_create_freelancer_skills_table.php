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
        Schema::create('freelancer_skill', function (Blueprint $table) {
            $table->id('freelancerskill_id');
            $table->unsignedBigInteger('freelancer_id');
            $table->unsignedBigInteger('skill_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelancer_skill');
    }
};
