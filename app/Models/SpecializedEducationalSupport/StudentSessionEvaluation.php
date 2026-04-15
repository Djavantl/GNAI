<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Builder;
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

    protected $casts = [
        'is_present' => 'boolean',
    ];

    public function sessionRecord(): BelongsTo
    {
        return $this->belongsTo(SessionRecord::class, 'session_record_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeOfStudent(Builder $query, int $studentId): Builder
    {
        return $query->where('student_id', $studentId);
    }
}