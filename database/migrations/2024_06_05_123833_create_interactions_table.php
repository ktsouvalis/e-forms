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
         Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stakeholder_id');
            $table->string('stakeholder_type');
            $table->unsignedBigInteger('interaction_type_id')->nullable();
            $table->text('text');
            $table->text('files')->nullable(); //json
            $table->string('protocol_number')->nullable()->default(null);
            $table->boolean('resolved')->default(false);
            $table->timestamps();

            $table->foreign('interaction_type_id')->references('id')->on('interaction_types')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
