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
        Schema::table('report', function (Blueprint $table) {
            $table->string('order_id')->nullable()->change();
            $table->unsignedBigInteger('user_id')->after('report_id')->nullable();
            $table->foreign('user_id')
                ->references('user_id')
                ->on('user');
            $table->string('report_type')->after('order_id');
            $table->string('status')->default('open')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('report_type');
            $table->dropColumn('status');
        });
    }
};
