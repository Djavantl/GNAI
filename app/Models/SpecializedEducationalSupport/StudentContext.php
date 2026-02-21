<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Auditable; // 1. Importar a Trait
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StudentContext extends Model
{
    use HasFactory, Auditable; // 2. Adicionar a Trait
    
    protected $table = 'student_contexts';

    protected $fillable = [
        'student_id',
        'semester_id',
        'evaluation_type',
        'is_current',
        'evaluated_by_professional_id',
        'history',
        'specific_educational_needs',
        'learning_level',
        'attention_level',
        'memory_level',
        'reasoning_level',
        'learning_observations',
        'communication_type',
        'interaction_level',
        'socialization_level',
        'shows_aggressive_behavior',
        'shows_withdrawn_behavior',
        'behavior_notes',
        'autonomy_level',
        'needs_mobility_support',
        'needs_communication_support',
        'needs_pedagogical_adaptation',
        'uses_assistive_technology',
        'has_medical_report',
        'uses_medication',
        'medical_notes',
        'strengths',
        'difficulties',
        'recommendations',
        'general_observation',
        'version',
    ];

    /**
     * Relacionamento com Logs de Auditoria
     */
    public function logs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * Labels amigáveis para os campos (Muitos campos para cobrir o diagnóstico)
     */
    public static function getAuditLabels(): array
    {
        return [
            // Geral e Identificação
            'semester_id'                  => 'Semestre da Avaliação',
            'evaluation_type'              => 'Tipo de Avaliação',
            'is_current'                   => 'Contexto Atual',
            'evaluated_by_professional_id' => 'Profissional Avaliador',
            'history'                      => 'Histórico do Aluno',
            'specific_educational_needs'   => 'Necessidades Educacionais Específicas',

            // Aprendizagem
            'learning_level'               => 'Nível de Aprendizagem',
            'attention_level'              => 'Nível de Atenção',
            'memory_level'                 => 'Nível de Memória',
            'reasoning_level'              => 'Nível de Raciocínio',
            'learning_observations'        => 'Observações de Aprendizagem',

            // Comunicação e Comportamento
            'communication_type'           => 'Tipo de Comunicação',
            'interaction_level'            => 'Nível de Interação',
            'socialization_level'          => 'Nível de Socialização',
            'shows_aggressive_behavior'    => 'Apresenta Comportamento Agressivo',
            'shows_withdrawn_behavior'     => 'Apresenta Comportamento Retraído',
            'behavior_notes'               => 'Notas Comportamentais',

            // Apoios e Autonomia
            'autonomy_level'               => 'Nível de Autonomia',
            'needs_mobility_support'       => 'Necessita Apoio de Mobilidade',
            'needs_communication_support'  => 'Necessita Apoio de Comunicação',
            'needs_pedagogical_adaptation' => 'Necessita Adaptação Pedagógica',
            'uses_assistive_technology'    => 'Usa Tecnologia Assistiva',

            // Saúde e Finalização
            'has_medical_report'           => 'Possui Laudo Médico',
            'uses_medication'              => 'Usa Medicação',
            'medical_notes'                => 'Notas Médicas',
            'strengths'                    => 'Pontos Fortes',
            'difficulties'                 => 'Dificuldades',
            'recommendations'              => 'Recomendações',
            'general_observation'          => 'Observação Geral',
        ];
    }

    /**
     * Formatação dos valores para o histórico
     */
    public static function formatAuditValue(string $field, $value): ?string
    {
        // Campos Booleanos (Sim/Não)
        $booleanFields = [
            'is_current', 'shows_aggressive_behavior', 'shows_withdrawn_behavior',
            'needs_mobility_support', 'needs_communication_support', 
            'needs_pedagogical_adaptation', 'uses_assistive_technology', 
            'has_medical_report', 'uses_medication'
        ];

        if (in_array($field, $booleanFields)) {
            return $value ? 'Sim' : 'Não';
        }

        // Relacionamentos
        if ($field === 'semester_id') {
            return \App\Models\SpecializedEducationalSupport\Semester::find($value)?->name ?? "ID: $value";
        }

        if ($field === 'evaluated_by_professional_id') {
            return \App\Models\SpecializedEducationalSupport\Professional::find($value)?->person?->name ?? "ID: $value";
        }

        return null;
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(Professional::class, 'evaluated_by_professional_id');
    }
}