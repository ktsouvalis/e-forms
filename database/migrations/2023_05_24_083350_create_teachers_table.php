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
        Schema::create('teachers', function (Blueprint $table) {
            //application
            $table->id();
            $table->string('md5');
            $table->timestamps();
            
            //personal
            $table->string('name');
            $table->string('surname');
            $table->string('fname');
            $table->string('mname');
            $table->string('afm');
            $table->string('gender');
            
            //contact
            $table->string('telephone')->nullable();
            $table->string('mail');
            $table->string('sch_mail')->nullable();
            
            // work
            $table->string('klados');
            $table->string('am')->nullable();
            $table->foreignId('sxesi_ergasias_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('org_eae');
            $table->date('appointment_date')->nullable();
            $table->string('appointment_fek')->nullable();
            //connections
            $table->unsignedBigInteger('organiki_id'); // οργανική τοποθέτηση: id απο τον πίνακα schools ή από τον πίνακα directories
            $table->string('organiki_type'); // όνομα  Model (School ή Directory)
            $table->unsignedBigInteger('ypiretisi_id')->nullable(); // υπηρέτηση: id απο τον πίνακα schools ή από τον πίνακα no_schools
            $table->string('ypiretisi_type')->nullable(); // όνομα  Model (School ή NoSchool)

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
