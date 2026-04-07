<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Traits\Reportable;

class Pei extends Model
{
    use HasFactory;
    use Reportable;

    protected $fillable = [
        'student_id',
        'creator_id',
        'semester_id',
        'course_id',
        'student_context_id',
        'is_finished',
        'version',
        'is_current',
    ];

    protected $casts = [
        'is_finished' => 'boolean',
        'is_current' => 'boolean',
    ];

    public static function getReportLabel(): string
    {
        return 'Planos Educacionais Individualizados (PEI)';
    }

    public static function getReportColumns(): array
    {
        return [
            'id',
            'version',
            'is_finished',
            'created_at',

            // relações principais
            'student.person.name',
            'course.name',
            'semester.label',
            'creator.name',
        ];
    }


    public static function getReportColumnLabels(): array
    {
        return [
            'id' => 'ID do PEI',
            'version' => 'Versão',
            'is_finished' => 'Finalizado',
            'is_current' => 'Versão Atual',
            'created_at' => 'Data de Criação',

            'student.person.name' => 'Aluno',
            'course.name' => 'Curso',
            'semester.label' => 'Semestre',
            'creator.name' => 'Criado por',
        ];
    }
    
    /**
     * Relacionamentos Principais
     */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

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

    public function studentContext(): BelongsTo
    {
        return $this->belongsTo(StudentContext::class);
    }

    public function peiDisciplines(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        // O pei_id é a chave estrangeira na tabela pei_disciplines
        return $this->hasMany(PeiDiscipline::class, 'pei_id');
    }

    public function getTeacherDisplayNameAttribute(): string
    {   
        return $this->teacher->person->name ?? 'Professor s/ Nome';
    }

    public function getCreatorNameAttribute(): string
    {
        if ($this->creator->is_admin) {
            return 'admin'; 
        } elseif ($this->creator->name) {
            return $this->creator->name; 
        } 

        return 'Sistema/Desconhecido';
    }

    public function scopeForContext($query, $studentId, $courseId, $disciplineId)
    {
        return $query->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('discipline_id', $disciplineId);
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

    public function scopeFinished($query, $isFinished)
    {
        if ($isFinished === null || $isFinished === '') return $query;

        return $query->where('is_finished', (bool) $isFinished);
    }

    public function scopeVersion($query, ?int $version)
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