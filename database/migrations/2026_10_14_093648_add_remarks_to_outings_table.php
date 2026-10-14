<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/2026_10_14_000000_add_remarks_to_outings_table.php
    public function up(): void
    {
        Schema::table('outings', function (Blueprint $table) {
            $table->text('remarks')->nullable();
            $table->timestamp('remarks_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('outings', function (Blueprint $table) {
            $table->dropColumn(['remarks', 'remarks_at']);
        });
    }
};
