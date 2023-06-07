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
        Schema::create('microapps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('active'); // inactive for users and stakeholders
            $table->boolean('visible'); // visible for stakeholders
            $table->boolean('accepts'); // accepts submissions from stakeholders
            $table->string('url');
            $table->dateTime('opens_at')->nullable(); // opens acceptability
            $table->dateTime('closes_at')->nullable(); // closes acceptability
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('microapps');
    }
};
