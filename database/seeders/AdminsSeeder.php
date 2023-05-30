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
        ]);
        
        User::create([
            'username' => 'kstefanopoulos',
            'display_name' => 'Κωνσταντίνος Στεφανόπουλος',
            'email' => 'konstantinostef@yahoo.gr',
            'password' => bcrypt('123456'),
        ]);

        //CREATE OPERATIONS
        Operation::create([
            'name' => 'Διαχείριση Χρηστών Διεύθυνσης',
            'url' => '/manage_users',
            'color' => 'skyblue',
            'icon' => 'fa-solid fa-users',
            'accepts'=> 0,
            'visible'=> 0
        ]);        

        // ASSIGN OPERATIONS TO USERS
        UsersOperations::create([
            'user_id'=>1,
            'operation_id'=>1
        ]);

        UsersOperations::create([
            'user_id'=>2,
            'operation_id'=>1
        ]);
    }
}
