<?php

namespace Database\Seeders;

use App\Models\Municipality;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Municipality::create([
            'name'=>'ΠΑΤΡΕΩΝ'
        ]);
        Municipality::create([
            'name'=>'ΑΙΓΙΑΛΕΙΑΣ'
        ]);
        Municipality::create([
            'name'=>'ΔΥΤΙΚΗΣ ΑΧΑΪΑΣ'
        ]);
        Municipality::create([
            'name'=>'ΕΡΥΜΑΝΘΟΥ'
        ]);
        Municipality::create([
            'name'=>'ΚΑΛΑΒΡΥΤΩΝ'
        ]);
    }
}
