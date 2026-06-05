<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments_classes', function (Blueprint $table) {
            $table->unsignedSmallInteger('integration_class_students')->nullable()->after('all_day_school_classes');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments_classes', function (Blueprint $table) {
            $table->dropColumn('integration_class_students');
        });
    }
};