<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\RolesMenus;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesMenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::create([
            'name' => 'Διαχείριση Χρηστών',
            'url' => '/manage_users',
            'color' => 'text-bg-primary',
            'opacity' => 'opacity: 0.4',
            'icon' => 'fa-solid fa-users'
        ]);

        Menu::create([
            'name' => 'Διαχείριση Ρόλων',
            'url' => '/manage_roles',
            'color' => 'text-bg-danger',
            'opacity' => 'opacity: 0.4',
            'icon' => 'bi bi-person-rolodex'
        ]);

        Menu::create([
            'name' => 'Αποσύνδεση',
            'url' => '/logout',
            'color' => 'text-bg-dark',
            'opacity' => 'opacity: 0.7',
            'icon' => 'fa-solid fa-arrow-right-from-bracket'
        ]);

        RolesMenus::create([
            'menu_id' => 1,
            'role_id' => 1
        ]);

        RolesMenus::create([
            'menu_id' => 2,
            'role_id' => 1
        ]);

    }
}
