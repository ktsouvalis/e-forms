<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SchoolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        School::create([
            'name' => '1ο Δημοτικό Σχολείο Πάτρας',
            'code' => '9060234',
            'md5' => '9d910788a391de2e6e21d58ad663c85b',
            'mail' => 'mail@1dim-patron.ach.sch.gr',
            'special_needs' => 0,
            'dim' => 1,
            'active' => 1,
            'international' => 1
        ]);

        School::create([
            'name' => '2ο Δημοτικό Σχολείο Πάτρας',
            'code' => '9060237',
            'md5' => '9d910788a391de2e6e21d58ad663c8cc',
            'mail' => 'mail@2dim-patron.ach.sch.gr',
            'special_needs' => 0,
            'dim' => 1,
            'active' => 1,
            'international' => 1
        ]);
        School::create([
            'name' => '1ο Νηπιαγωγείο Πάτρας',
            'code' => '9060296',
            'md5' => '9d910788a391de2e6e21d58ad663c8dd',
            'mail' => 'mail@1nip-patron.ach.sch.gr',
            'special_needs' => 0,
            'dim' => 0,
            'active' => 1,
            'international' => 1
        ]);
        School::create([
            'name' => 'Αρσάκειο Δημοτικό Σχολείο Πάτρας',
            'code' => '7061000',
            'md5' => '9d910788a391de2e6e21d58ad663c8ee',
            'mail' => 'dhm-p@arsakeio.gr',
            'special_needs' => 0,
            'dim' => 1,
            'active' => 1,
            'international' => 0
        ]);
    }
}
