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
        Schema::table('occupation', function (Blueprint $table) {
            $table->foreign('freelancer_id')
                ->references('freelancer_id')
                ->on('freelancer')
                ->onDelete('cascade');

                $table->foreign('category_id')
                ->references('category_id')
                ->on('category')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('occupation', function (Blueprint $table) {
            $table->dropForeign(['freelancer_id']);
            $table->dropForeign(['category_id']);
        });
    }
};
