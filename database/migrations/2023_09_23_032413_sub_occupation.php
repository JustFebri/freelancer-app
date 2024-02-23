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
        Schema::create('sub_occupation', function (Blueprint $table) {
            $table->id('suboccupation_id');
            $table->unsignedBigInteger('freelancer_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_occupation');
    }
};
