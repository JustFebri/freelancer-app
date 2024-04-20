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
        Schema::create('personal_url', function (Blueprint $table) {
            $table->id('url_id');
            $table->unsignedBigInteger('freelancer_id');
            $table->text('personalUrl');

            $table->foreign('freelancer_id')
                ->references('freelancer_id')
                ->on('freelancer')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_url');
    }
};
