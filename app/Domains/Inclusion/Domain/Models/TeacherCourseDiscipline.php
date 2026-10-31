<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TeacherCourseDiscipline extends Model implements Auditable
{
    use AuditsChanges;

    protected $fillable = [
        'teacher_id',
        'course_id',
        'discipline_id',
    ];

    public static function auditType(): string
    {
        return 'teacher_course_discipline';
    }

    public static function auditLabel(): string
    {
        return 'Disciplina do Professor';
    }

    public static function auditedFields(): array
    {
        return [
            'teacher_id' => AuditedField::reference('Professor', Teacher::class, 'person.name'),
            'course_id' => AuditedField::reference('Curso', Course::class),
            'discipline_id' => AuditedField::reference('Disciplina', Discipline::class),
        ];
    }

    public function auditDescription(): string
    {
        $teacherName = $this->teacher?->person?->name ?? "#{$this->teacher_id}";
        $disciplineName = $this->discipline?->name ?? "#{$this->discipline_id}";
        $courseName = $this->course?->name ?? "#{$this->course_id}";

        return "a disciplina {$disciplineName} do professor {$teacherName} em {$courseName}";
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }
}
