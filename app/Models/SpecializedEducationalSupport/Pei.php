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
    ];

    protected $casts = [
        'is_finished' => 'boolean',
    ];

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
}