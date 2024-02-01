<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Operation;
use App\Models\Superadmin;
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
            'username' => 'admin',
            'display_name' => 'Διαχειριστής',
            'email' => 'something@somewhere.gr',
            'password' => bcrypt('123456'),
            'department_id' => 5
        ]);

        Superadmin::create([
            'user_id' => 1
        ]);
    }
}
