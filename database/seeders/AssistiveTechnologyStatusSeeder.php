<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AssistiveTechnologyStatusSeeder extends Seeder
{
    public function run()
    {
        \App\Models\InclusiveRadar\AssistiveTechnologyStatus::insert([
            ['name' => 'Ativo'],
            ['name' => 'Inativo'],
            ['name' => 'Em manutenção'],
        ]);
    }

}
