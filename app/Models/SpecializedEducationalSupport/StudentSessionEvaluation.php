<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSessionEvaluation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'session_record_id',
        'student_id',
        'is_present',
        'absence_reason',
        'adaptations_made',
        'student_participation',
        'development_evaluation',
        'progress_indicators',
        'recommendations',
        'next_session_adjustments',
    ];

    /**
     * Relacionamento com o registro geral da sessão
     */
    public function sessionRecord(): BelongsTo
    {
        return $this->belongsTo(SessionRecord::class, 'session_record_id');
    }

    /**
     * Relacionamento com o Aluno
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class); // Ajuste o namespace se necessário
    }
}