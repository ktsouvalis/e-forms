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
        Schema::table('sections', function ($table) {
            $table->string('sec_code')->nullable();
        });
    }

    public function down()
    {
        Schema::table('sections', function ($table) {
            $table->dropColumn('sec_code');
        });
    }
};
