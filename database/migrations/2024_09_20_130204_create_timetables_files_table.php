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
        Schema::create('timetables_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_id')->constrained()->cascadeOnDelete();
            $table->text('filenames_json')->nullable();
            $table->text('timestamps_json')->nullable();
            $table->string('comments')->nullable();
            $table->integer('status')->default(0); // 0: Αρχική Υποβολή, 1: Αναμονή Υποβολής Διορθώσεων, 2: Υποβλήθηκαν Διορθώσεις, 3: Εγκρίθηκε
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables_files');
    }
};
