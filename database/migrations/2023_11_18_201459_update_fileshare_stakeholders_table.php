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
        Schema::table('fileshares_stakeholders', function ($table) {
            $table->unsignedBigInteger('addedby_id'); //  id απο τον πίνακα schools ή από τον πίνακα users ή από τον πίνακα teachers
            $table->string('addedby_type'); // όνομα  Model (School ή User)
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
