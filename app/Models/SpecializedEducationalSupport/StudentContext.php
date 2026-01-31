<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentContext extends Model
{
    use HasFactory;
    
    protected $table = 'student_contexts';

    protected $fillable = [
        'student_id',

        // Aprendizagem
        'learning_level',
        'attention_level',
        'memory_level',
        'reasoning_level',
        'learning_observations',

        // Comunicação e interação
        'communication_type',
        'interaction_level',
        'socialization_level',
        'shows_aggressive_behavior',
        'shows_withdrawn_behavior',
        'behavior_notes',

        // Autonomia e apoio
        'autonomy_level',
        'needs_mobility_support',
        'needs_communication_support',
        'needs_pedagogical_adaptation',
        'uses_assistive_technology',

        // Saúde
        'has_medical_report',
        'uses_medication',
        'medical_notes',

        // Avaliação geral
        'strengths',
        'difficulties',
        'recommendations',
        'general_observation',
    ];

    // Dono do contexto
     
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
