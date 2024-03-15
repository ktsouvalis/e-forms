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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->text('comments')->nullable();
            $table->integer('nr_of_students1');
            $table->string('enrolled_file1');
            $table->integer('nr_of_students1_all_day1')->nullable();
            $table->string('all_day_file1')->nullable();
            $table->string('extra_section_file1')->nullable();
            $table->string('boundaries_st_file1')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};