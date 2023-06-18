<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Department::create([
            'name'=>'Γραμματεία Διευθυντή'
        ]);

        Department::create([
            'name'=>'Τμήμα Διοίκησης'
        ]);

        Department::create([
            'name'=>'Τμήμα Προσωπικού'
        ]);

        Department::create([
            'name'=>'Τμήμα Οικονομικού'
        ]);

        Department::create([
            'name'=>'Τμήμα Πληροφορικής'
        ]);

        Department::create([
            'name'=>'Τμήμα Εκπαιδευτικών Θεμάτων'
        ]);
        
        Department::create([
            'name'=>'Περιβαλλοντική Εκπαίδευση '
        ]);

        Department::create([
            'name'=>'Πολιτιστικά Θέματα'
        ]);

        Department::create([
            'name'=>'Αγωγή Υγείας'
        ]);

        Department::create([
            'name'=>'Φυσικής Αγωγής'
        ]);
    }
}
