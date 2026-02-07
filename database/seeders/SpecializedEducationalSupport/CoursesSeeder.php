<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Course;

class CoursesSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['name' => 'Técnico em Informática', 'description' => 'Curso técnico com foco em desenvolvimento de software e redes.'],
            ['name' => 'Técnico em Administração', 'description' => 'Curso técnico com foco em práticas administrativas e gestão.'],
            ['name' => 'Técnico em Mecânica', 'description' => 'Curso técnico com foco em processos industriais e mecânica.'],
        ];

        foreach ($courses as $data) {
            Course::create(array_merge($data, ['is_active' => true]));
        }
    }
}
