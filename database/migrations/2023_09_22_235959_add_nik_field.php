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
            $table->string('identity_name')->after('identity_number')->nullable();
            $table->string('identity_gender')->after('identity_name')->nullable();
            $table->string('identity_address')->after('identity_gender')->nullable();
            $table->string('IsApproved')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freelancer', function (Blueprint $table) {
            $table->dropColumn('identity_name');
            $table->dropColumn('identity_gender');
            $table->dropColumn('identity_address');
            $table->dropColumn('IsApproved');
        });
    }
};
