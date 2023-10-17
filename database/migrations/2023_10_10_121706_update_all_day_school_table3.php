<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::table('all_day_school', function ($table) {
            $table->string('nr_morning');
        });
    }

    public function down()
    {
        Schema::table('all_day_school', function ($table) {
            $table->dropColumn('nr_morning');
        });
    }
};
