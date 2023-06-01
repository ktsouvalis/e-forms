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
        Schema::create('forms_stakeholders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            
            $table->unsignedBigInteger('stakeholder_id'); // Που απευθύνεται: id απο τον πίνακα schools ή από τον πίνακα teachers
            $table->string('stakeholder_type'); // όνομα  Model (School ή Teacher)

            $table->json('answers');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms_stakeholders');
    }
};
