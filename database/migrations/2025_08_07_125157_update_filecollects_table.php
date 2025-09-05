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
        Schema::table('filecollects', function (Blueprint $table) {
            // Add new columns
            $table->dateTime('opens_at')->nullable()->after('accepts'); // opens acceptability
            $table->dateTime('closes_at')->nullable()->after('opens_at'); // closes acceptability

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
