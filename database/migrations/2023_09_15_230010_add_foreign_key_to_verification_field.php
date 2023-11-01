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
        Schema::table('freelancer', function (Blueprint $table) {
            $table->foreign('id_card')
                ->references('picture_id')
                ->on('picture')
                ->onDelete('cascade');
            $table->foreign('id_card_with_selfie')
                ->references('picture_id')
                ->on('picture')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freelancer', function (Blueprint $table) {
            $table->dropForeign(['id_card']);
            $table->dropForeign(['id_card_with_selfie']);
        });
    }
};
