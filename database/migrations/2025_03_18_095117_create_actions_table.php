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
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('actiontype_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('implementing_authority');
            $table->string('title', 500);
            $table->integer('number_of_teachers')->default(0);
            $table->string('teachers')->nullable();
            $table->string('records')->nullable();
            $table->string('comments')->nullable();
            $table->string('status')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
