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
        Schema::table('all_day_school', function (Blueprint $table) {
            $table->text('comments')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('all_day_schools', function (Blueprint $table) {
            $table->text('comments')->nullable(false)->change();
        });
    }
};
