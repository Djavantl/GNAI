<?php

namespace Database\Seeders\SpecializedEducationalSupport;

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

            // Contexto antigo (Versão 1)
            StudentContext::query()->updateOrCreate([
                'student_id' => $student->id,
                'version' => 1,
            ], [
                'semester_id' => $previousSemester->id,
                'evaluated_by_professional_id' => $professionals->random()->id,
                'evaluation_type' => 'initial',
                'is_current' => false,

                // Histórico e necessidades
                'history' => 'Aluno ingressou na instituição com histórico de dificuldades de adaptação escolar e necessidade de acompanhamento pedagógico.',
                'specific_educational_needs' => 'Necessita de mediação pedagógica contínua, adaptações metodológicas e acompanhamento do AEE.',

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
                'needs_mobility_support' => null,
                'needs_communication_support' => null,
                'needs_pedagogical_adaptation' => '<p>Necessita de adaptações metodológicas nas atividades avaliativas e materiais pedagógicos em formato acessível.</p>',
                'uses_assistive_technology' => null,

                // Saúde
                'has_medical_report' => true,
                'uses_medication' => false,
                'medical_notes' => 'Laudo médico arquivado.',

                // Observações gerais (Ajustado)
                'knowledge' => 'Boa memória visual e conhecimentos básicos de rotina escolar.', 
                'difficulties' => 'Dificuldade de concentração prolongada.',
                
            ]);

            // Contexto atual (Versão 2)
            StudentContext::query()->updateOrCreate([
                'student_id' => $student->id,
                'version' => 2,
            ], [
                'semester_id' => $currentSemester->id,
                'evaluated_by_professional_id' => $professionals->random()->id,
                'evaluation_type' => 'periodic_review',
                'is_current' => true,

                // Histórico e necessidades
                'history' => 'Aluno apresenta evolução progressiva desde o ingresso, com melhora significativa na participação em sala.',
                'specific_educational_needs' => 'Manutenção de estratégias pedagógicas diferenciadas, com foco em atividades estruturadas e apoio pontual.',

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
                'needs_mobility_support' => null,
                'needs_communication_support' => null,
                'needs_pedagogical_adaptation' => null,
                'uses_assistive_technology' => null,

                // Saúde
                'has_medical_report' => true,
                'uses_medication' => false,
                'medical_notes' => null,

                // Observações gerais (Ajustado)
                'knowledge' => 'Boa participação, autonomia e domínio dos conteúdos ministrados no semestre anterior.',
                'difficulties' => 'Ainda apresenta dificuldade em atividades longas.',
                
            ]);
        }
    }
}
