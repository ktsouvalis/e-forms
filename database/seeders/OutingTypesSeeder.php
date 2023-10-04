<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OutingTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('outingtypes')->insert(['description'=>'ΟΛΙΓΟΩΡΗ']);
        DB::table('outingtypes')->insert(['description'=>'ΠΟΛΥΩΡΗ']);
        DB::table('outingtypes')->insert(['description'=>'ΗΜΕΡΗΣΙΑ']);
    }
}
