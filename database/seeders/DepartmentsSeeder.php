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
            'name'=>'Γραμματείας Διευθυντή'
        ]);

        Department::create([
            'name'=>"Τμήμα A' Διοικητικού"
        ]);

        Department::create([
            'name'=>"Τμήμα Β' Οικονομικού"
        ]);
        
        Department::create([
            'name'=>"Τμήμα Γ' Προσωπικού"
        ]);

        Department::create([
            'name'=>"Τμήμα Δ' Πληροφορικής"
        ]);

        Department::create([
            'name'=>"Τμήμα Ε' Εκπαιδευτικών Θεμάτων"
        ]);
        
        Department::create([
            'name'=>'Περιβαλλοντικής Εκπαίδευσης '
        ]);

        Department::create([
            'name'=>'Πολιτιστικών Θεμάτων'
        ]);

        Department::create([
            'name'=>'Αγωγής Υγείας'
        ]);

        Department::create([
            'name'=>'Φυσικής Αγωγής'
        ]);
    }
}
