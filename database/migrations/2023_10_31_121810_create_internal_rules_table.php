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
        Schema::create('internal_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('school_file')->nullable();
            $table->string('school_file2')->nullable();
            $table->string('school_file3')->nullable();
            $table->string('consultant_comments_file')->nullable();
            $table->string('director_comments_file')->nullable();
            $table->boolean('approved_by_consultant')->default(false);
            $table->boolean('approved_by_director')->default(false);
            $table->string('consultant_signed_file')->nullable();
            $table->string('director_signed_file')->nullable();
            $table->dateTime('school_updated_at')->nullable();
            $table->dateTime('consultant_signed_at')->nullable();
            $table->dateTime('director_signed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_rules');
    }
};
