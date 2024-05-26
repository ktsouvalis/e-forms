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
        Schema::create('enrollments_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('morning_zone_classes')->nullable();
            $table->string('morning_classes', 1000)->nullable();
            $table->string('all_day_school_classes')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments_classes');
    }
};
