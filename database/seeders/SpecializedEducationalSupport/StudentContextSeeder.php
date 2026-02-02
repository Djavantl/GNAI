<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Semester;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentContext;
use App\Models\SpecializedEducationalSupport\Professional;

class StudentContextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $professionals = Professional::all();

        // Semestres
        $currentSemester = Semester::where('is_current', true)->first();
        $previousSemester = Semester::where('is_current', false)
            ->orderByDesc('year')
            ->orderByDesc('term')
            ->first();

        if (!$currentSemester || !$previousSemester) {
            throw new \Exception('Semestres insuficientes para criar contextos.');
        }

        foreach ($students as $student) {

            // Contexto antigo
            StudentContext::create([
                'student_id' => $student->id,
                'semester_id' => $previousSemester->id,
                'evaluated_by_professional_id' => $professionals->random()->id,
                'evaluation_type' => 'initial',
                'is_current' => false,

                // Aprendizagem
                'learning_level' => 'low',
                'attention_level' => 'moderate',
                'memory_level' => 'moderate',
                'reasoning_level' => 'concrete',
                'learning_observations' => 'Apresentava dificuldades iniciais de adaptação.',

                // Comunicação
                'communication_type' => 'verbal',
                'interaction_level' => 'low',
                'socialization_level' => 'selective',
                'shows_aggressive_behavior' => false,
                'shows_withdrawn_behavior' => true,
                'behavior_notes' => 'Pouca interação no início do semestre.',

                // Autonomia
                'autonomy_level' => 'partial',
                'needs_mobility_support' => false,
                'needs_communication_support' => false,
                'needs_pedagogical_adaptation' => true,
                'uses_assistive_technology' => false,

                // Saúde
                'has_medical_report' => true,
                'uses_medication' => false,
                'medical_notes' => 'Laudo médico arquivado.',

                // Avaliação geral
                'strengths' => 'Boa memória visual.',
                'difficulties' => 'Dificuldade de concentração prolongada.',
                'recommendations' => 'Uso de atividades curtas e mediadas.',
                'general_observation' => 'Contexto inicial do aluno.',
            ]);

            // Contexto atual
            StudentContext::create([
                'student_id' => $student->id,
                'semester_id' => $currentSemester->id,
                'evaluated_by_professional_id' => $professionals->random()->id,
                'evaluation_type' => 'periodic_review',
                'is_current' => true,

                // Aprendizagem
                'learning_level' => 'adequate',
                'attention_level' => 'high',
                'memory_level' => 'good',
                'reasoning_level' => 'mixed',
                'learning_observations' => 'Apresenta evolução significativa.',

                // Comunicação
                'communication_type' => 'verbal',
                'interaction_level' => 'good',
                'socialization_level' => 'participative',
                'shows_aggressive_behavior' => false,
                'shows_withdrawn_behavior' => false,
                'behavior_notes' => 'Interage bem com colegas e professores.',

                // Autonomia
                'autonomy_level' => 'independent',
                'needs_mobility_support' => false,
                'needs_communication_support' => false,
                'needs_pedagogical_adaptation' => false,
                'uses_assistive_technology' => false,

                // Saúde
                'has_medical_report' => true,
                'uses_medication' => false,
                'medical_notes' => null,

                // Avaliação geral
                'strengths' => 'Boa participação e autonomia.',
                'difficulties' => 'Ainda apresenta dificuldade em atividades longas.',
                'recommendations' => 'Manter estratégias já aplicadas.',
                'general_observation' => 'Contexto atual do aluno.',
            ]);
        }
    }
}
