<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssistiveTechnologyStatusSeeder extends Seeder
{
    public function run()
    {
        \App\Models\AssistiveTechnologyStatus::insert([
            ['name' => 'Ativo'],
            ['name' => 'Inativo'],
            ['name' => 'Em manutenção'],
        ]);
    }

}
