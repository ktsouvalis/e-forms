<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {   
        $this->call([
            DepartmentsSeeder::class,
            AdminsSeeder::class,
            MunicipalitySeeder::class,
            SxesiErgasiasSeeder::class,
            DirectoriesSeeder::class,
            MonthSeeder::class,
            NoSchoolSeeder::class,
            OutingTypesSeeder::class,
            ConsultantsSeeder::class,
            SchregionSeeder::class,
        ]);
    }
}
