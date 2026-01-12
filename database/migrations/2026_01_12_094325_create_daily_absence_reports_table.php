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
        Schema::create('daily_absence_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->date('report_date');
            $table->integer('absent_count')->unsigned();
            $table->text('comments')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();
            // Ensure one report per school per day
            $table->unique(['school_id', 'report_date']);
            
            // Index for quick queries
            $table->index('report_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_absence_reports');
    }
};
