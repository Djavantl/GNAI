<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'attendance_session_id', 
        'duration',
        'activities_performed',
        'strategies_used',
        'resources_used',
        'general_observations',
    ];

    /**
     * Relacionamento com a Sessão de Atendimento (Pai)
     */
    public function attendanceSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'attendance_session_id')->withTrashed();
    }

    /**
     * Relacionamento com as avaliações individuais dos alunos (Filhos)
     */
    public function studentEvaluations(): HasMany
    {
        return $this->hasMany(StudentSessionEvaluation::class, 'session_record_id');
    }
}