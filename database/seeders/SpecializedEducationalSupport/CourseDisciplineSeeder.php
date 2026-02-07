<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\Discipline;

class CourseDisciplineSeeder extends Seeder
{
    public function run(): void
    {
        // Busca todos (assumindo que já foram criados pelos seeders anteriores)
        $courseInfo = Course::where('name', 'Técnico em Informática')->first();
        $courseAdm  = Course::where('name', 'Técnico em Administração')->first();
        $courseMech = Course::where('name', 'Técnico em Mecânica')->first();

        $prog = Discipline::where('name', 'Programação I')->first();
        $db   = Discipline::where('name', 'Banco de Dados')->first();
        $math = Discipline::where('name', 'Matemática')->first();
        $hr   = Discipline::where('name', 'Gestão de Pessoas')->first();

        // Associação (evita duplicação usando syncWithoutDetaching)
        if ($courseInfo) {
            $courseInfo->disciplines()->syncWithoutDetaching([$prog->id, $db->id, $math->id]);
        }

        if ($courseAdm) {
            $courseAdm->disciplines()->syncWithoutDetaching([$math->id, $hr->id]);
        }

        if ($courseMech) {
            $courseMech->disciplines()->syncWithoutDetaching([$math->id]);
        }
    }
}
