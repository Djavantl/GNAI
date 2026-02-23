<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Semester;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\Discipline;

class PeiSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $professional = Professional::first(); // Profissional que assina o PEI
        $semester = Semester::where('is_current', true)->first() ?? Semester::first();
        
        // Contexto do Aluno (supondo ID 1 para exemplo, ajuste se necessário)
        $contextId = DB::table('student_contexts')->first()->id ?? 1;

        foreach ($students as $student) {
            // Descobrir qual curso o aluno está matriculado
            $course = $student->courses()->first() ?? Course::first();
            
            // Pegar 3 disciplinas desse curso para criar 3 PEIs diferentes
            $disciplines = $course->disciplines()->limit(3)->get();

            foreach ($disciplines as $discipline) {
                // 1. Criar o PEI
                $peiId = DB::table('peis')->insertGetId([
                    'student_id' => $student->id,
                    'professional_id' => $professional->id,
                    'semester_id' => $semester->id,
                    'course_id' => $course->id,
                    'discipline_id' => $discipline->id,
                    'teacher_name' => 'Professor(a) de ' . $discipline->name,
                    'student_context_id' => $contextId,
                    'is_finished' => rand(0, 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 2. Criar Conteúdo Programático
                DB::table('content_programmatic')->insert([
                    'pei_id' => $peiId,
                    'title' => 'Adaptação Curricular - ' . $discipline->name,
                    'description' => 'Redução de complexidade nos cálculos e foco em conceitos práticos.',
                    'created_at' => now(),
                ]);

                // 3. Criar Metodologia
                DB::table('methodologies')->insert([
                    'pei_id' => $peiId,
                    'description' => 'Uso de mapas mentais e tempo estendido para provas.',
                    'resources_used' => 'Lupa eletrônica, software de leitura de tela.',
                    'created_at' => now(),
                ]);

                // 4. Criar Objetivos Específicos
                DB::table('specific_objectives')->insert([
                    'pei_id' => $peiId,
                    'description' => 'Compreender os fundamentos básicos da disciplina.',
                    'status' => 'Em andamento',
                    'observations_progress' => 'O aluno demonstra interesse, mas necessita de reforço.',
                    'created_at' => now(),
                ]);

                // 5. Criar 2 Avaliações para este PEI
                for ($i = 1; $i <= 2; $i++) {
                    DB::table('pei_evaluations')->insert([
                        'pei_id' => $peiId,
                        'evaluation_instruments' => 'Prova adaptada e trabalho em grupo.',
                        'semester_id' => $semester->id,
                        'parecer' => "Avaliação $i: O aluno atingiu os objetivos propostos para este bimestre.",
                        'successful_proposals' => 'A utilização de recursos visuais facilitou a fixação.',
                        'next_stage_goals' => 'Aumentar a autonomia na resolução de exercícios.',
                        'evaluated_by_professional_id' => $professional->id,
                        'evaluation_type' => $i == 1 ? 'Parcial' : 'Final',
                        'evaluation_date' => now()->format('Y-m-d'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}