<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UsersRoles;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        Role::create([
            'name' => 'superuser'
        ]);

        UsersRoles::create([
            'user_id'=>1,
            'role_id'=>1
        ]);

        UsersRoles::create([
            'user_id'=>2,
            'role_id'=>1
        ]);
    }
}
