<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Auditable; // 1. Importar a Trait
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StudentCourse extends Model
{
    use HasFactory, Auditable; // 2. Adicionar a Trait

    protected $table = 'student_courses';

    protected $fillable = [
        'student_id',
        'course_id',
        'academic_year',
        'is_current',
    ];

    /**
     * Relacionamento com Logs de Auditoria
     */
    public function logs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * Labels amigáveis para o Log e PDF
     */
    public static function getAuditLabels(): array
    {
        return [
            'course_id'     => 'Curso',
            'academic_year' => 'Ano Acadêmico',
            'is_current'    => 'Curso Atual',
        ];
    }

    /**
     * Formatação dos valores para o histórico
     */
    public static function formatAuditValue(string $field, $value): ?string
    {
        if ($field === 'course_id') {
            return \App\Models\SpecializedEducationalSupport\Course::find($value)?->name ?? "ID: $value";
        }

        if ($field === 'is_current') {
            return $value ? 'Sim' : 'Não';
        }

        return null;
    }

    // RELACIONAMENTOS

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    // Scopes para Filtros
    public function scopeCourseId($query, ?int $courseId)
    {
        if (!$courseId) return $query;
        return $query->where('course_id', $courseId);
    }

    public function scopeAcademicYear($query, ?string $year)
    {
        if (!$year) return $query;
        return $query->where('academic_year', 'like', "%{$year}%");
    }

    public function scopeIsCurrent($query, $isCurrent)
    {
        if ($isCurrent === null || $isCurrent === '') return $query;
        return $query->where('is_current', (bool) $isCurrent);
    }
}