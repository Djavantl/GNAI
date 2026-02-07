<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Discipline;

class DisciplinesSeeder extends Seeder
{
    public function run(): void
    {
        $disciplines = [
            ['name' => 'Programação I', 'description' => 'Lógica de programação, algoritmos e estruturas de dados.'],
            ['name' => 'Banco de Dados', 'description' => 'Modelagem, SQL e administração de bancos de dados.'],
            ['name' => 'Matemática', 'description' => 'Matemática aplicada ao curso técnico.'],
            ['name' => 'Gestão de Pessoas', 'description' => 'Noções de RH, liderança e trabalho em equipe.'],
        ];

        foreach ($disciplines as $data) {
            Discipline::create(array_merge($data, ['is_active' => true]));
        }
    }
}
