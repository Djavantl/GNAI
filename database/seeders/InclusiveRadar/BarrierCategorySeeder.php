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
                'blocks_map' => false,
            ],
            [
                'name' => 'Comunicacional',
                'description' => 'Barreiras relacionadas à comunicação e à informação.',
                'blocks_map' => true,
            ],
            [
                'name' => 'Atitudinal',
                'description' => 'Barreiras decorrentes de preconceitos ou atitudes excludentes.',
                'blocks_map' => true,
            ],
            [
                'name' => 'Pedagógica',
                'description' => 'Barreiras no processo de ensino e aprendizagem.',
                'blocks_map' => true,
            ],
            [
                'name' => 'Tecnológica',
                'description' => 'Barreiras relacionadas ao uso ou acesso à tecnologia.',
                'blocks_map' => true,
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
