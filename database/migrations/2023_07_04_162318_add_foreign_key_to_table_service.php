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
        Schema::table('service', function (Blueprint $table) {
            $table->foreign('subcategory_id')
                ->references('subcategory_id')
                ->on('sub_category')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
        });
    }
};
