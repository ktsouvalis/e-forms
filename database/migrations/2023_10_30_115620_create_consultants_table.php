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
        Schema::create('consultants', function (Blueprint $table) {
            //application
            $table->id();
            $table->string('md5');
            $table->timestamps();
            
            //personal
            $table->string('name');
            $table->string('surname');
            $table->string('afm');
            
            //contact
            $table->string('telephone')->nullable();
            $table->string('mail')->nullable();
            
            // work
            $table->string('klados');
            $table->string('am');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultants');
    }
};
