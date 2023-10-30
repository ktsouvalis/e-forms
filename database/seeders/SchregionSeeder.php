<?php

namespace Database\Seeders;

use App\Models\Schregion;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SchregionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Schregion::create(['name' => '1η Θέση Σ.Ε. Δασκάλων Αχαΐας',
        'consultant_id' => 9,]);

        Schregion::create(['name' => '2η Θέση Σ.Ε. Δασκάλων Αχαΐας',
        'consultant_id' => 4,]);

        Schregion::create(['name' => '3η Θέση Σ.Ε. Δασκάλων Αχαΐας',
        'consultant_id' => 6,]);

        Schregion::create(['name' => '4η Θέση Σ.Ε. Δασκάλων Αχαΐας',
        'consultant_id' => 7,]);

        Schregion::create(['name' => '5η Θέση Σ.Ε. Δασκάλων Αχαΐας',
        'consultant_id' => 1,]);

        Schregion::create(['name' => '6η Θέση Σ.Ε. Δασκάλων Αχαΐας',
        'consultant_id' => 3,]);

        Schregion::create(['name' => '7η Θέση Σ.Ε. Δασκάλων Αχαΐας',
        'consultant_id' => 10,]);

        Schregion::create(['name' => '1η Θέση Σ.Ε. Νηπιαγωγών Αχαΐας',
        'consultant_id' => 8,]);

        Schregion::create(['name' => '2η Θέση Σ.Ε. Νηπιαγωγών Αχαΐας',
        'consultant_id' => 5,]);

        Schregion::create(['name' => 'Θέση Σ.Ε. Ειδικής Αγωγής & Ενταξιακής Εκπαίδευσης',
        'consultant_id' => 2,]);
    }
}
