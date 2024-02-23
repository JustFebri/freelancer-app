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
        Schema::create('freelancer_language', function (Blueprint $table) {
            $table->id('freelancerlanguage_id');
            $table->unsignedBigInteger('freelancer_id');
            $table->unsignedBigInteger('language_id');
            $table->string('proficiency_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelancer_language');
    }
};
