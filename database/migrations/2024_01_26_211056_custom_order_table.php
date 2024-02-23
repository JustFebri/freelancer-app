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
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id('custom_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('freelancer_id');
            $table->text('description');
            $table->decimal('price', 10, 0);
            $table->unsignedInteger('revision');
            $table->unsignedInteger('delivery_days');
            $table->timestamps();

            $table->string('status');
            $table->timestamp('expiration_date')->nullable();

            $table->foreign('freelancer_id')
                ->references('freelancer_id')
                ->on('freelancer')
                ->onDelete('cascade');

            $table->foreign('service_id')
                ->references('service_id')
                ->on('service')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_orders', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['freelancer_id']);
        });


        Schema::dropIfExists('custom_orders');
    }
};
