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
        Schema::create('service_package', function (Blueprint $table) {
            $table->increments('package_id');
            $table->unsignedInteger('service_id');
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 0);
            $table->unsignedInteger('revision');
            $table->unsignedInteger('delivery_days');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_package');
    }
};
