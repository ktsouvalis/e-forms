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
        Schema::create('secondments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->boolean('statement_of_declaration')->default(0)->comment('0: Όχι, 1: Υπεύθυνη Δήλωση');
            $table->boolean('application_for_reposition')->default(0)->comment('0: Οχι, 1: Αίτηση για βελτίωση θέσης το τρέχον έτος');
            $table->boolean('special_category')->default(0)->comment('0: Οχι, 1: Ειδική Κατηγορία');
            $table->boolean('health_issues')->default(0)->comment('0: Οχι, 1: Λόγοι Υγείας Ιδίου');
            $table->boolean('parents_health_issues')->default(0)->comment('0: Οχι, 1: Λόγοι Υγείας Γονέων');
            $table->foreignId('parents_municipality')->nullable()->constrained('municipalities')->comment('Δήμος Κατοικίας Γονέων');
            $table->boolean('siblings_health_issues')->default(0)->comment('0: Οχι, 1: Λόγοι Υγείας Αδελφών');
            $table->foreignId('siblings_municipality')->nullable()->constrained('municipalities')->comment('Δήμος Κατοικίας Αδελφών');
            $table->boolean('IVF')->default(0)->comment('0: Οχι, 1: Εξωσωματική Θεραπεία');
            $table->boolean('post_graduate_studies')->default(0)->comment('0: Οχι, 1: Μεταπτυχιακές Σπουδές');
            $table->foreignId('studies_municipality')->nullable()->constrained('municipalities')->comment('Δήμος Σπουδών');
            $table->tinyInteger('marital_status')->default(0)->comment('0: Άγαμος, 1: Έγγαμος, 2: Διαζευγμένος');
            $table->tinyInteger('nr_of_children')->default(0)->comment('0: Οχι, 1: Ένα, 2: Δύο, 3: Τρία, κλπ');
            $table->foreignId('civil_status_municipality')->nullable()->constrained('municipalities')->comment('Δήμος Οικογενειακής Κατάστασης');
            $table->foreignId('living_municipality')->nullable()->constrained('municipalities')->comment('Δήμος Εντοπιότητας');
            $table->foreignId('partner_working_municipality')->nullable()->constrained('municipalities')->comment('Δήμος Εργασίας Συζύγου');
            $table->text('comments')->nullable();
            $table->date('submit_date')->nullable();
            $table->boolean('submitted')->default(0);
            $table->text('files_json')->nullable();
            $table->text('preferences_json')->nullable();
            $table->text('preferences_comments')->nullable();
            $table->string('protocol_nr')->nullable();
            $table->string('protocol_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secondments');
    }
};
