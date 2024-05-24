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
        //
        Schema::table('enrollments', function($table){
            $table->integer('nr_of_sections1')->nullable();
            $table->integer('myschool_nr_of_students2')->nullable();
            $table->integer('myschool_nr_of_sections2')->nullable();
            $table->integer('nr_of_students2')->nullable();
            $table->integer('nr_of_sections2')->nullable();
            $table->integer('myschool_nr_of_students3')->nullable();
            $table->integer('myschool_nr_of_sections3')->nullable();
            $table->integer('nr_of_students3')->nullable();
            $table->integer('nr_of_sections3')->nullable();
            $table->integer('myschool_nr_of_students4')->nullable();
            $table->integer('myschool_nr_of_sections4')->nullable();
            $table->integer('nr_of_students4')->nullable();
            $table->integer('nr_of_sections4')->nullable();
            $table->integer('myschool_nr_of_students5')->nullable();
            $table->integer('myschool_nr_of_sections5')->nullable();
            $table->integer('nr_of_students5')->nullable();
            $table->integer('nr_of_sections5')->nullable();
            $table->integer('myschool_nr_of_students6')->nullable();
            $table->integer('myschool_nr_of_sections6')->nullable();
            $table->integer('nr_of_students6')->nullable();
            $table->integer('nr_of_sections6')->nullable();
            // Αριθμός τμημάτων ολοήμερου
            $table->integer('nr_of_students_all_day1')->nullable();
            $table->integer('nr_of_sections_all_day1')->nullable();
            $table->integer('nr_of_students_all_day2')->nullable();
            $table->integer('nr_of_sections_all_day2')->nullable();
            $table->integer('nr_of_students_all_day3')->nullable();
            $table->integer('nr_of_sections_all_day3')->nullable();
            //Πρωινή Ζώνη
            $table->integer('morning_zone_nr_of_students')->nullable();
            $table->integer('morning_zone_nr_of_sections')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
