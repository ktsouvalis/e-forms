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
        Schema::create('teacher_leaves', function (Blueprint $table) {
            $table->id();
            $table->string('am')->nullable();
            $table->string('afm');
            $table->foreign('afm')->references('afm')->on('teachers')->onDelete('cascade');
            $table->string('sex');
            $table->string('surname');
            $table->string('name');
            $table->string('fathers_name');
            $table->string('specialty_code');
            $table->string('specialty');
            $table->string('directorate')->nullable();
            $table->string('employment_relation')->nullable();
            $table->string('leave_state')->nullable();
            $table->string('leave_type')->nullable();
            $table->date('leave_start_date')->nullable();
            $table->integer('leave_days')->nullable();
            $table->string('leave_protocol_number')->nullable();
            $table->date('leave_protocol_date')->nullable();
            $table->string('leave_description', 500)->nullable();
            $table->string('creator_entity_code')->nullable();
            $table->string('creator_entity_name')->nullable();
            $table->date('creation_date')->nullable();
            $table->string('submission_date')->nullable();
            $table->string('approved_days')->nullable();
            $table->string('approved_months')->nullable();
            $table->string('approved_years')->nullable();
            $table->string('approved_protocol_number')->nullable();
            $table->date('approved_protocol_date')->nullable();
            $table->string('approved_description')->nullable();
            $table->string('revoke_description')->nullable();
            $table->string('approving_authority_code')->nullable();
            $table->string('approving_authority_name')->nullable();
            $table->date('last_change_date')->nullable();
            $table->text('files_json')->nullable();
            $table->text('comments')->nullable();
            $table->string('protocol_number')->nullable();
            $table->date('protocol_date')->nullable();
            $table->boolean('submitted')->default(0);
            $table->text('approved_files_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_leaves');
    }
};
