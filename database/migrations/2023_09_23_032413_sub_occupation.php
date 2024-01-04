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
            $table->increments('suboccupation_id');
            $table->unsignedInteger('freelancer_id')->nullable();
            $table->unsignedInteger('subcategory_id')->nullable();
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
