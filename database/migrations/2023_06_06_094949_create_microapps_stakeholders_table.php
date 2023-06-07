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
        Schema::create('microapps_stakeholders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('microapp_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            
            $table->unsignedBigInteger('microstakeholder_id'); // Που απευθύνεται: id απο τον πίνακα schools ή από τον πίνακα teachers
            $table->string('microstakeholder_type'); // όνομα  Model (School ή Teacher)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('microapps_stakeholders');
    }
};
