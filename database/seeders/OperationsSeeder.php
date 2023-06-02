<?php

namespace Database\Seeders;

use App\Models\Operation;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class operationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //CREATE ADMINS
        Operation::create([
            'name' => 'Σχολεία',
            'url' => '/schools',
            'color' => 'LightYellow',
            'icon' => 'bi bi-buildings',
        ]);

        Operation::create([
            'name' => 'Εκπαιδευτικοί',
            'url' => '/teachers',
            'color' => 'NavajoWhite',
            'icon' => 'bi bi-person-video3',
        ]);

    }
}
