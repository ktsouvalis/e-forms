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
        Schema::create('swimming', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->string('mobile_phone')->nullable();
            $table->boolean('specialty')->default(0);
            $table->string('specialty_files_json')->nullable();
            $table->boolean('licence')->default(0);
            $table->string('licence_files_json')->nullable();
            $table->boolean('studied')->default(0);
            $table->string('studied_files_json')->nullable();
            $table->boolean('coordinator')->default(0);
            $table->boolean('teacher')->default(0);
            $table->string('files_json', 500)->nullable();
            $table->text('comments')->nullable();
            $table->boolean('submitted')->default(0);
            $table->boolean('revoked')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swimming');
    }
};
