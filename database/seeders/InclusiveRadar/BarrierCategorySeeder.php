<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BarrierCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            [
                'name' => 'Arquitetônica',
                'description' => 'Barreiras físicas ou estruturais que dificultam o acesso.',
            ],
            [
                'name' => 'Comunicacional',
                'description' => 'Barreiras relacionadas à comunicação e à informação.',
            ],
            [
                'name' => 'Atitudinal',
                'description' => 'Barreiras decorrentes de preconceitos ou atitudes excludentes.',
            ],
            [
                'name' => 'Pedagógica',
                'description' => 'Barreiras no processo de ensino e aprendizagem.',
            ],
            [
                'name' => 'Tecnológica',
                'description' => 'Barreiras relacionadas ao uso ou acesso à tecnologia.',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('barrier_categories')->updateOrInsert(
                ['name' => $category['name']],
                array_merge($category, [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
