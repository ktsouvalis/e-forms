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
        Schema::table('filecollects_stakeholders', function (Blueprint $table) {
            $table->string('file', 2048)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('filecollects_stakeholders', function (Blueprint $table) {
            $table->string('file', 255)->change();
        });
    }
};
