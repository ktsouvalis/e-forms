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
        Schema::create('ticket_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id');
            $table->longText('text');
            $table->unsignedBigInteger('ticketer_id'); //  id απο τον πίνακα schools ή από τον πίνακα users
            $table->string('ticketer_type'); // όνομα  Model (School ή User)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_posts');
    }
};
