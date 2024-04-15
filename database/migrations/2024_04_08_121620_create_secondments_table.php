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
            $table->boolean('health_issues')->comment('0: Οχι, 1: Λόγοι Υγείας Ιδίου')->nullable();
            $table->boolean('parents_health_issues')->comment('0: Οχι, 1: Λόγοι Υγείας Γονέων')->nullable();
            $table->foreignId('parents_municipality')->nullable()->constrained('municipalities')->comment('Δήμος Κατοικίας Γονέων');
            $table->boolean('siblings_health_issues')->comment('0: Οχι, 1: Λόγοι Υγείας Αδελφών')->nullable();
            $table->foreignId('siblings_municipality')->nullable()->constrained('municipalities')->comment('Δήμος Κατοικίας Αδελφών');
            $table->boolean('IVF')->comment('0: Οχι, 1: Εξωσωματική Θεραπεία')->nullable();
            $table->boolean('post_graduate_studies')->comment('0: Οχι, 1: Μεταπτυχιακές Σπουδές')->nullable();
            $table->foreignId('studies_municipality')->nullable()->constrained('municipalities')->comment('Δήμος Σπουδών');
            $table->tinyInteger('marital_status')->default(0)->comment('0: Άγαμος, 1: Έγγαμος, 2: Διαζευγμένος');
            $table->tinyInteger('nr_of_children')->default(0)->comment('0: Οχι, 1: Ένα, 2: Δύο, 3: Τρία, κλπ');
            $table->string('civil_status_municipality')->comment('Δήμος Οικογενειακής Κατάστασης - ελεύθερο κείμενο')->nullable();
            $table->foreignId('living_municipality')->nullable()->constrained('municipalities')->comment('Δήμος Εντοπιότητας');
            $table->foreignId('partner_working_municipality')->nullable()->constrained('municipalities')->comment('Δήμος Εργασίας Συζύγου');
            $table->boolean('application_for_reposition')->default(0)->comment('0: Οχι, 1: Αίτηση για βελτίωση θέσης το τρέχον έτος');
            $table->boolean('special_category')->default(0)->comment('0: Οχι, 1: Ειδική Κατηγορία');
            $table->boolean('general_to_special_secondment')->comment('0: Οχι, 1: Απόσπαση από τη Γενική στην Ειδική Αγωγή')->nullable();
            $table->boolean('special_needs_phd')->comment('0: Οχι, 1: Διδακτορικό στην Ειδική Αγωγή')->nullable();
            $table->boolean('special_needs_msc')->comment('0: Οχι, 1: Μεταπτυχιακό στην Ειδική Αγωγή')->nullable();
            $table->boolean('special_needs_bsc')->comment('0: Οχι, 1: Προπτυχιακό στην Ειδική Αγωγή')->nullable();
            $table->integer('special_needs_years')->comment('Έτη προϋπηρεσίας στην Ειδική Αγωγή')->nullable();
            $table->integer('special_needs_months')->comment('Μήνες προϋπηρεσίας στην Ειδική Αγωγή')->nullable();
            $table->integer('special_needs_days')->comment('Ημέρεσ προϋπηρεσίας στην Ειδική Αγωγή')->nullable();
            $table->boolean('special_needs_position')->comment('0: Οχι, 1: Οργανική Θέση στην Ειδική Αγωγή')->nullable();
            $table->string('special_needs_qualification')->comment('Άλλο προσόν στην Ειδική Αγωγή')->nullable();
            $table->boolean('plus_general_education')->comment('0: Οχι, 1: Αίτηση και για τη Γενική Αγωγή')->nullable();
            $table->text('comments')->nullable();
            $table->boolean('statement_of_declaration')->default(0)->comment('0: Όχι, 1: Υπεύθυνη Δήλωση');
            $table->date('submit_date')->nullable();
            $table->boolean('submitted')->default(0);
            $table->text('files_json')->nullable();
            $table->text('preferences_json')->nullable();
            $table->text('preferences_comments')->nullable();
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
