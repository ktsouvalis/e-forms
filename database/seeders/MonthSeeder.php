<?php

namespace Database\Seeders;

use App\Models\Month;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Month::create([
           'name' => 'ΙΑΝΟΥΑΡΙΟΣ',
           'number' => 1
        ]);

        Month::create([
           'name' => 'ΦΕΒΡΟΥΑΡΙΟΣ',
           'number' => 2
        ]);

        Month::create([
           'name' => 'ΜΑΡΤΙΟΣ',
           'number' => 3
        ]);

        Month::create([
           'name' => 'ΑΠΡΙΛΙΟΣ',
           'number' => 4
        ]);

        Month::create([
           'name' => 'ΜΑΪΟΣ',
           'number' => 5
        ]);

        Month::create([
           'name' => 'ΙΟΥΝΙΟΣ',
           'number' => 6
        ]);

        Month::create([
           'name' => 'ΙΟΥΛΙΟΣ',
           'number' => 7
        ]);

        Month::create([
           'name' => 'ΑΥΓΟΥΣΤΟΣ',
           'number' => 8
        ]);

        Month::create([
           'name' => 'ΣΕΠΤΕΜΒΡΙΟΣ',
           'number' => 9,
           'active' => 1
        ]);

        Month::create([
           'name' => 'ΟΚΤΩΒΡΙΟΣ',
           'number' => 10
        ]);

        Month::create([
           'name' => 'ΝΟΕΜΒΡΙΟΣ',
           'number' => 11
        ]);

        Month::create([
           'name' => 'ΔΕΚΕΜΒΡΙΟΣ',
           'number' => 12
        ]);
    }
}
