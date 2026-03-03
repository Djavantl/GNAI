<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use App\Models\InclusiveRadar\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'institution_id' => 1, // ajuste para a instituição correta
                'name' => 'Refeitorio',
                'type' => 'Restaurante / Refeitório',
                'description' => 'Área destinada às refeições dos alunos e funcionários.',
                'latitude' => -14.30138673,
                'longitude' => -42.69287109,
                'google_place_id' => null,
                'is_active' => true,
            ],
            [
                'institution_id' => 1,
                'name' => 'Lab.1 - ADS',
                'type' => 'Laboratório de Informática',
                'description' => 'Laboratório do curso de Análise e Desenvolvimento de Sistemas.',
                'latitude' => -14.30228232,
                'longitude' => -42.69311971,
                'google_place_id' => null,
                'is_active' => true,
            ],
            [
                'institution_id' => 1,
                'name' => 'Quadra de Futsal',
                'type' => 'Quadra Esportiva',
                'description' => 'Espaço para prática de futsal e esportes internos.',
                'latitude' => -14.30251363,
                'longitude' => -42.69403173,
                'google_place_id' => null,
                'is_active' => true,
            ],
            [
                'institution_id' => 1,
                'name' => 'Campo de Futebol',
                'type' => 'Campo Esportivo',
                'description' => 'Campo aberto para prática de futebol e atividades externas.',
                'latitude' => -14.30339992,
                'longitude' => -42.69446329,
                'google_place_id' => null,
                'is_active' => true,
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
