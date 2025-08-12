<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table is used to map school usernames to their respective school codes.
     * It is filled with a direct SQL Query in the database. There is no seeder for this table nor other Procedure to fill it.
     */
    public function up(): void
    {
        Schema::create('school_username_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('school_code')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_username_mappings');
    }
};
