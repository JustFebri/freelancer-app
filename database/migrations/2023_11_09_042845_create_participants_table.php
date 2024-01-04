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
        Schema::create('participants', function (Blueprint $table) {
            $table->increments('participant_id');
            $table->unsignedInteger('chatRoom_id');
            $table->unsignedInteger('user_id');
            $table->unique(['chatRoom_id', 'user_id']);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('user')
                ->cascadeOnDelete();

            $table->foreign('chatRoom_id')
                ->references('chatRoom_id')
                ->on('chat_rooms')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
