<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pei extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'professional_id',
        'semester_id',
        'course_id',
        'discipline_id',
        'teacher_name',
        'student_context_id',
        'is_finished',
        'version',
        'is_current',
    ];

    protected $casts = [
        'is_finished' => 'boolean',
    ];

    public function scopeCurrent($query)
    {
        return $q->where('is_current', true);
    }

    public function scopeForContext($query, $studentId, $courseId, $disciplineId)
    {
        return $query->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('discipline_id', $disciplineId);
    }

    /**
     * Relacionamentos Principais
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    public function studentContext(): BelongsTo
    {
        return $this->belongsTo(StudentContext::class);
    }

    /**
     * Tabelas Auxiliares (Adaptações Curriculares)
     */
    public function specificObjectives(): HasMany
    {
        return $this->hasMany(SpecificObjective::class);
    }

    public function contentProgrammatic(): HasMany
    {
        return $this->hasMany(ContentProgrammatic::class);
    }

    public function methodologies(): HasMany
    {
        return $this->hasMany(Methodology::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(PeiEvaluation::class, 'pei_id');
    }

    public function scopeStudent($query, ?int $studentId)
    {
        if (!$studentId) return $query;

        return $query->where('student_id', $studentId);
    }

    public function scopeSemester($query, ?int $semesterId)
    {
        if (!$semesterId) return $query;

        return $query->where('semester_id', $semesterId);
    }

    public function scopeDiscipline($query, ?int $disciplineId)
    {
        if (!$disciplineId) return $query;

        return $query->where('discipline_id', $disciplineId);
    }

    public function scopeFinished($query, $isFinished)
    {
        if ($isFinished === null || $isFinished === '') return $query;

        return $query->where('is_finished', (bool) $isFinished);
    }

    public function scopeVersion($query, ?string $version)
    {
        if (!$version) return $query;

        return $query->where('version', $version);
    }
}