<?php

namespace Database\Seeders;

use App\Models\ClassName;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClassNamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        ClassName::create([
            'name'=>'Α',
            'comments'=>'6Θέσιο και άνω',
        ]);
        ClassName::create([
            'name'=>'Β',
            'comments'=>'6Θέσιο και άνω',
        ]);
        ClassName::create([
            'name'=>'Γ',
            'comments'=>'6Θέσιο και άνω',
        ]);
        ClassName::create([
            'name'=>'Δ',
            'comments'=>'6Θέσιο και άνω',
        ]);
        ClassName::create([
            'name'=>'Ε',
            'comments'=>'6Θέσιο και άνω',
        ]);
        ClassName::create([
            'name'=>'ΣΤ',
            'comments'=>'6Θέσιο και άνω',
        ]);
        ClassName::create([
            'name'=>'ΤΜΗΜΑ ΟΛΙΓΟΘΕΣΙΟΥ',
            'comments'=>'5Θέσιο και κάτω',
        ]);
        ClassName::create([
            'name'=>'ΠΡΟΝΗΠΙΑ-ΝΗΠΙΑ',
            'comments'=>'Νηπιαγωγείο',
        ]);
        ClassName::create([
            'name'=>'ΠΡΩΙΝΗ ΖΩΝΗ',
        ]);
        ClassName::create([
            'name'=>'ΟΛΟΗΜΕΡΟ ΖΩΝΗ 1',
        ]);
        ClassName::create([
            'name'=>'ΟΛΟΗΜΕΡΟ ΖΩΝΗ 2',
        ]);
        ClassName::create([
            'name'=>'ΟΛΟΗΜΕΡΟ ΖΩΝΗ 3',
        ]);

    }
}
