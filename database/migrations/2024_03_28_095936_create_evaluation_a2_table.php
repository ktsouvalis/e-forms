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
        Schema::create('evaluation_a2', function (Blueprint $table) {
            $table->id();
            $table->string('teacher_afm');
            $table->enum('category', ['Νεοδιόριστος', 'Πειραματικό', 'Επιθυμία', 'Σειρά διορισμού', 'Αλλο']);
            $table->string('evaluator_afm');
            $table->text('evaluator_afm_comments')->nullable();
            $table->string('school_code')->nullable();
            $table->text('school_comments')->nullable();
            $table->date('date_in');
            $table->timestamp('self_evaluation_date')->nullable();
            $table->date('date_out')->nullable();
            $table->enum('date_out_reason', ['Άδεια', 'Μετάθεση', 'Απόσπαση', 'Αλλο'])->nullable();
            $table->text('date_out_reason_comments')->nullable();
            $table->date('date_completed')->nullable();
            $table->timestamp('date_completed_timestamp')->nullable();
            $table->text('date_completed_comments')->nullable();
            $table->boolean('completed_n_given')->default(false);
            $table->text('comments')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_a2');
    }
};
