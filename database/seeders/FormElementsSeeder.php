<?php

namespace Database\Seeders;

use App\Models\FormElements;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FormElementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        FormElements::create([
            'type' => 'text',
            'attributes' => '{"1":"label", "2":"placeholder", "3":"required", "4":"disabled", "5":"size"}'
        ]);

        FormElements::create([
            'type' => 'href',
            'attributes' => '{"1":"filenameWithPath", "2":"filenameToShow", "3":"downloadName" }'
        ]);

    }
}
