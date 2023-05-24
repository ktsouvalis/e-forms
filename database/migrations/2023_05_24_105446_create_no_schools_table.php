<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

     // Άλλες υπηρεσίες (τύποι αποσπάσεων π.χ. στο εξωτερικό, σε άλλο πυσπε, σε άλλο πυσδε κλπ). Όλα τα είδη υπηρετήσεων εκτός των σχολείων μας.
    public function up(): void
    {
        Schema::create('no_schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('no_schools');
    }
};
