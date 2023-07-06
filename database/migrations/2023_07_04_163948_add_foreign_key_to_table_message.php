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
        Schema::table('message', function (Blueprint $table) {
            $table->foreign('chatroom_id')
                ->references('chatroom_id')
                ->on('chat_room')
                ->onDelete('cascade');

            $table->foreign('client_id')
                ->references('client_id')
                ->on('client')
                ->onDelete('cascade');

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
        Schema::table('message', function (Blueprint $table) {
            $table->dropForeign(['chatroom_id']);
            $table->dropForeign(['client_id']);
            $table->dropForeign(['freelancer_id']);
        });
    }
};
