<?php

namespace Database\Seeders;

use App\Models\SxesiErgasias;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SxesiErgasiasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        SxesiErgasias::create([
            'monimos'=>1,
            'name'=>'Μόνιμος'
        ]);

        SxesiErgasias::create([
            'monimos'=>0,
            'name'=>'Αναπληρωτής Ειδικής μέσω ΠΔΕ'
        ]);

        SxesiErgasias::create([
            'monimos'=>0,
            'name'=>'Αναπληρωτής Παράλληλης Στήριξης μέσω ΕΣΠΑ'
        ]);

        SxesiErgasias::create([
            'monimos'=>0,
            'name'=>'Αναπληρωτής Ειδικής'
        ]);

        SxesiErgasias::create([
            'monimos'=>0,
            'name'=>'Αναπληρωτής μέσω ΕΣΠΑ'
        ]);

        SxesiErgasias::create([
            'monimos'=>0,
            'name'=>'Αναπληρωτής ΠΔΕ'
        ]);

        SxesiErgasias::create([
            'monimos'=>0,
            'name'=>'Αναπληρωτής Μειωμένου Ωραρίου μέσω ΕΣΠΑ'
        ]);

        SxesiErgasias::create([
            'monimos'=>0,
            'name'=>'Αναπληρωτής ΖΕΠ μέσω ΕΣΠΑ'
        ]);

        SxesiErgasias::create([
            'monimos'=>0,
            'name'=>'Αναπληρωτής ΠΔΕ Μειωμένου Ωραρίου'
        ]);

        SxesiErgasias::create([
            'monimos'=>0,
            'name'=>'Αναπληρωτής'
        ]);

        SxesiErgasias::create([
            'monimos'=>0,
            'name'=>'Αναπληρωτής Παράλληλης Στήριξης'
        ]);
    }
}
