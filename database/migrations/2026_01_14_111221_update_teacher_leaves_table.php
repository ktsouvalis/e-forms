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
        Schema::table('teacher_leaves', function (Blueprint $table) {
            // Column for user to hide/display the leave
            $table->boolean('is_visible')->default(1)->after('submitted');
            
            // Column to relate a leave to another leave (self-referencing foreign key)
            $table->unsignedBigInteger('related_leave_id')->nullable()->after('is_visible');
            $table->foreign('related_leave_id')->references('id')->on('teacher_leaves')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_leaves', function (Blueprint $table) {
            $table->dropForeign(['related_leave_id']);
            $table->dropColumn(['is_visible', 'related_leave_id']);
        });
    }
};
