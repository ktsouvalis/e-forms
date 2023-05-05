<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use App\Models\UsersMenus;
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
        Menu::create([
            'name' => 'Διαχείριση Χρηστών',
            'url' => '/manage_users',
            'color' => 'skyblue',
            'icon' => 'fa-solid fa-users'
        ]);

        Menu::create([
            'name' => 'Διαχείριση Menu',
            'url' => '/manage_menus',
            'color' => 'PaleTurquoise',
            'icon' => 'bi bi-menu-button-wide'
        ]);

        

        // ASSIGN OPERATIONS TO USERS
        UsersMenus::create([
            'user_id'=>1,
            'menu_id'=>1
        ]);

        UsersMenus::create([
            'user_id'=>2,
            'menu_id'=>1
        ]);

        UsersMenus::create([
            'user_id'=>1,
            'menu_id'=>2
        ]);

        UsersMenus::create([
            'user_id'=>2,
            'menu_id'=>2
        ]);
    }
}
