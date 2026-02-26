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
        'creator_id',
        'semester_id',
        'course_id',
        'discipline_id',
        'teacher_id',
        'teacher_name',
        'student_context_id',
        'is_finished',
        'version',
        'is_current',
    ];

    protected $casts = [
        'is_finished' => 'boolean',
    ];

    // Relacionamento com o professor do sistema
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function getTeacherDisplayNameAttribute(): string
    {
        if ($this->teacher_id && $this->teacher) {
            return $this->teacher->person->name ?? 'Professor s/ Nome';
        }

        return $this->teacher_name ?? 'Não informado';
    }

    public function creator(): BelongsTo
    {
        // Aponta para o model User usando a nova coluna creator_id
        return $this->belongsTo(\App\Models\User::class, 'creator_id');
    }

    /**
     * Retorna o nome de quem criou o PEI (seja profissional ou professor)
     * Uso no Blade: {{ $pei->creator_name }}
     */
    public function getCreatorNameAttribute(): string
    {
        if ($this->creator) {
            return $this->creator->name; 
        }

        return 'Sistema/Desconhecido';
    }

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

    public function scopeVisibleToUser($query, $user)
    {
        // só aplica a regra se for professor
        if (!$user->teacher_id) {
            return $query;
        }

        return $query->whereIn('discipline_id', function ($q) use ($user) {
            $q->select('discipline_id')
            ->from('discipline_teacher')
            ->where('teacher_id', $user->teacher_id);
        });
    }
}