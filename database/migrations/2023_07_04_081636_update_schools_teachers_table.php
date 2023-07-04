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
        //
        Schema::table('schools', function ($table) {
            $table->dateTime('logged_in_at')->nullable();
        });

        Schema::table('teachers', function ($table) {
            $table->dateTime('logged_in_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('schools', function ($table) {
            $table->dropColumn('logged_in_at');
        });

        Schema::table('teachers', function ($table) {
            $table->dropColumn('logged_in_at');
        });
    }
};
