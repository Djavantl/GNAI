<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Semester;
use Carbon\Carbon;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que não exista semestre atual duplicado antes de começar
        Semester::query()->update(['is_current' => false]);

        $semesters = [];
        $startYear = 2020; // Começando em 2020 para gerar 15 semestres (7.5 anos)
        $totalInstances = 15;

        for ($i = 0; $i < $totalInstances; $i++) {
            // Calcula o ano e o termo (1 ou 2) com base no índice
            $year = $startYear + floor($i / 2);
            $term = ($i % 2) + 1;
            
            // Define datas aproximadas
            $isFirstTerm = ($term === 1);
            $startDate = $isFirstTerm ? "$year-02-01" : "$year-08-01";
            $endDate = $isFirstTerm ? "$year-06-30" : "$year-12-15";
            
            // Define o semestre atual (Baseado na data de hoje: 2026/1)
            $isCurrent = ($year == 2026 && $term == 1);

            $semesters[] = [
                'year'       => $year,
                'term'       => $term,
                'label'      => "$year/$term",
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'is_current' => $isCurrent,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Semester::insert($semesters);
    }
}