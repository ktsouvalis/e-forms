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
        Schema::create('building_problems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('comments')->nullable()->comment('Type of problem (e.g., plumbing, electrical, structural)');
            $table->integer('severity')->default(0)->comment('Severity of the problem (1: Low, 2: Medium, 3: High)');
            $table->text('files_json')->nullable()->comment('JSON containing file paths or URLs related to the problem');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_problems');
    }
};
