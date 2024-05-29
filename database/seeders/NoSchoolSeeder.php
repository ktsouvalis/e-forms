<?php

namespace Database\Seeders;

use App\Models\NoSchool;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NoSchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        NoSchool::create([
            'name' => 'ΑΠΟΣΠΑΣΗ ΣΕ ΑΛΛΟ ΠΥΣΠΕ / ΠΥΣΔΕ'
        ]);

        NoSchool::create([
            'name' => 'ΑΠΟΣΠΑΣΗ ΣΕ ΦΟΡΕΑ ΕΚΤΟΣ ΥΠ. ΠΑΙΔΕΙΑΣ'
        ]);

        NoSchool::create([
            'name' => 'ΑΠΟΣΠΑΣΗ ΣΕ ΦΟΡΕΑ ΥΠ. ΠΑΙΔΕΙΑΣ'
        ]);

        NoSchool::create([
            'name' => 'ΑΠΟΣΠΑΣΗ ΣΤΟ ΕΞΩΤΕΡΙΚΟ'
        ]);

        NoSchool::create([
            'name' => 'ΘΕΣΗ ΕΚΠΑΙΔΕΥΣΗΣ ΕΠΙ ΘΗΤΕΙΑ'
        ]);

        NoSchool::create([
            'name' => 'ΜΑΚΡΟΧΡΟΝΙΑ ΑΔΕΙΑ (>10 ημέρες)'
        ]);

        NoSchool::create([
            'name' => 'ΟΛΙΚΗ ΔΙΑΘΕΣΗ ΣΕ ΑΠΟΚΕΝΤΡΩΜΕΝΕΣ ΥΠΗΡΕΣΙΕΣ ΥΠ. ΠΑΙΔΕΙΑΣ'
        ]);

        NoSchool::create([
            'name' => 'ΟΛΙΚΗ ΔΙΑΘΕΣΗ ΠΕ11'
        ]);

        NoSchool::create([
            'name' => 'ΜΗ ΑΠΟΔΕΣΜΕΥΣΗ'
        ]);        

        NoSchool::create([
            'name' => 'ΔΙΑΔΟΧΙΚΕΣ ΒΡΑΧΥΧΡΟΝΙΕΣ ΑΔΕΙΕΣ'
        ]);  
    }
}
