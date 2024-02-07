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
        Schema::create('filecollects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('department_id');
            $table->longText('comment')->nullable();
            $table->string('base_file')->nullable();
            $table->string('template_file')->nullable();
            $table->string('fileMime');
            $table->boolean('visible'); // visible for stakeholders
            $table->boolean('accepts'); // accepts submissions from stakeholders
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filecollects');
    }
};