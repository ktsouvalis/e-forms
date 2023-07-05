<?php

namespace Database\Seeders;

use App\Models\Operation;
use App\Models\User;
use App\Models\UsersOperations;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //CREATE ADMINS
        User::create([
            'username' => 'ktsouvalis',
            'display_name' => 'Κωνσταντίνος Τσούβαλης',
            'email' => 'ktsouvalis@sch.gr',
            'password' => bcrypt('123456'),
            'department_id' => 5
        ]);
        
        User::create([
            'username' => 'kstefanopoulos',
            'display_name' => 'Κωνσταντίνος Στεφανόπουλος',
            'email' => 'konstantinostef@yahoo.gr',
            'password' => bcrypt('123456'),
            'department_id' => 5
        ]);

        User::create([
            'username' => 'test',
            'display_name' => 'Δοκιμαστικός Χρήστης',
            'email' => 'it@dipe.ach.sch.gr',
            'password' => bcrypt('123456'),
            'department_id' => 3
        ]);


        //CREATE OPERATIONS

        Operation::create([
            'name' => 'Σχολεία',
            'url' => '/schools',
            'color' => 'MediumAquamarine',
            'icon' => 'fa-solid fa-school'
        ]);

        Operation::create([
            'name' => 'Εκπαιδευτικοί',
            'url' => '/teachers',
            'color' => 'LightSalmon',
            'icon' => 'fa-solid fa-chalkboard-user'
        ]);
    }
}
