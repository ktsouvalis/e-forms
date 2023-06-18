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
        Schema::create('fileshares_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fileshare_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fileshares_departments');
    }
};
