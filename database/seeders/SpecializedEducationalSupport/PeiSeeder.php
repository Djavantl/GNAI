<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Semester;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\Discipline;
use App\Enums\SpecializedEducationalSupport\ObjectiveStatus;

class PeiSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        
        // Agora buscamos o USER (pois creator_id aponta para users)
        $creator = User::first(); 
        
        // Profissional para as avaliações (ajuste conforme sua lógica de Professional vs User)
        $professional = Professional::first(); 

        $semester = Semester::where('is_current', true)->first() ?? Semester::first();
        
        // Pega o contexto atual de algum lugar ou do aluno
        $context = DB::table('student_contexts')->first();

        if (!$students->count() || !$creator || !$semester || !$context) {
            $this->command->warn('Certifique-se de ter Alunos, Usuários, Semestres e Contextos antes de rodar este Seeder.');
            return;
        }

        foreach ($students as $student) {
            // Descobrir o curso do aluno através da relação (student_courses)
            $course = $student->courses()->first() ?? Course::first();
            
            // Pegar disciplinas vinculadas a este curso
            $disciplines = $course->disciplines()->limit(2)->get();

            foreach ($disciplines as $discipline) {
                // 1. Criar o PEI (Ajustado para as novas colunas)
                $peiId = DB::table('peis')->insertGetId([
                    'student_id'         => $student->id,
                    'creator_id'         => $creator->id, 
                    'semester_id'        => $semester->id,
                    'course_id'          => $course->id,
                    'discipline_id'      => $discipline->id,
                    'teacher_id'         => null, 
                    'teacher_name'       => 'Prof. ' . fake()->name(),
                    'student_context_id' => $context->id,
                    'is_finished'        => false,
                    'version'            => 1,
                    'is_current'         => true,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);

                // 2. Criar Conteúdo Programático
                DB::table('content_programmatic')->insert([
                    'pei_id'      => $peiId,
                    'title'       => 'Adaptação Curricular - ' . $discipline->name,
                    'description' => 'Foco em competências essenciais e redução de carga teórica.',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                // 3. Criar Metodologia
                DB::table('methodologies')->insert([
                    'pei_id'         => $peiId,
                    'title'          => 'Adaptação do metodo de ensino',
                    'description'    => 'Uso de metodologias ativas e gamificação.',
                    'resources_used' => 'Projetor multimídia, softwares assistivos.',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                // 4. Criar Objetivos Específicos (Usando o Enum)
                DB::table('specific_objectives')->insert([
                    'pei_id'                => $peiId,
                    'title'                 => 'Desenvolvimento',
                    'description'           => 'Desenvolver autonomia na resolução de problemas de ' . $discipline->name,
                    'status'                => ObjectiveStatus::IN_PROGRESS->value,
                    'observations_progress' => 'Apresenta evolução constante.',
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);

                // 5. Avaliações (Verifique se a tabela pei_evaluations ainda usa professional_id ou se mudou para creator_id)
                DB::table('pei_evaluations')->insert([
                    'pei_id'                 => $peiId,
                    'evaluation_instruments' => 'Observação direta e portfólio.',
                    'semester_id'            => $semester->id,
                    'parecer'                => 'O aluno demonstra engajamento com as atividades adaptadas.',
                    'successful_proposals'   => 'O uso de reforço visual foi eficaz.',
                    'next_stage_goals'       => 'Introduzir conceitos de maior complexidade.',
                    'evaluated_by_professional_id' => $professional->id ?? null,
                    'evaluation_type'        => 'Parcial',
                    'evaluation_date'        => now()->format('Y-m-d'),
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);
            }
        }
    }
}