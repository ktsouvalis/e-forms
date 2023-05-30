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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            
            //properties start
            $table->string('name');
            $table->string('code');
            $table->string('municipality');
            $table->boolean('primary');
            $table->integer('leitourgikotita');
            $table->integer('organikotita');
            $table->string('telephone');
            $table->boolean('is_active');
            $table->boolean('has_all_day');
            $table->string('md5');
            $table->string('mail')->unique();
            $table->boolean('special_needs');
            $table->boolean('experimental');
            $table->boolean('international');
            
            //properties end
            $table->timestamp('email_verified_at')->nullable();
            //$table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
