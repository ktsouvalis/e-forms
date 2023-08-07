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
        Schema::create('all_day_school', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('month_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('functionality');
            $table->text('comments');
            $table->integer('nr_of_pupils_3');
            $table->integer('nr_of_class_3');
            $table->integer('nr_of_pupils_4');
            $table->integer('nr_of_class_4');
            $table->integer('nr_of_pupils_5');
            $table->integer('nr_of_class_5');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_day_school');
    }
};
