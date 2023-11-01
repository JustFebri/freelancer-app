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
            $table->string('link')->nullable();
            $table->unsignedInteger('id_card')->nullable();
            $table->unsignedInteger('id_card_with_selfie')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freelancer', function (Blueprint $table) {
            $table->dropColumn('link');
            $table->dropColumn('id_card');
            $table->dropColumn('id_card_with_selfie');
        });
    }
};
