<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Inclusion\Domain\DTOs\StudentCourses\CreateStudentCourseDTO;
use App\Domains\Inclusion\Domain\DTOs\StudentCourses\UpdateStudentCourseDTO;
use App\Domains\Inclusion\Domain\Enums\StudentCourseDisciplineCategory;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStudentCourse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

final class StudentCourse extends Pivot implements Auditable
{
    use AuditsChanges;
    use HasFactory;

    protected $table = 'student_courses';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'student_id',
        'course_id',
        'academic_year',
        'is_current',
        'school_attendance_status',
    ];

    protected $casts = [
        'academic_year' => 'integer',
        'is_current' => 'boolean',
    ];

    public static function auditType(): string
    {
        return 'student_course';
    }

    public static function auditLabel(): string
    {
        return 'Matrícula em Curso';
    }

    public static function auditedFields(): array
    {
        return [
            'student_id' => AuditedField::reference('Aluno', Student::class, 'person.name'),
            'course_id' => AuditedField::reference('Curso / Série', Course::class),
            'academic_year' => AuditedField::text('Ano Letivo'),
            'is_current' => AuditedField::boolean('Curso atual'),
            'school_attendance_status' => AuditedField::richText('Situação da Frequência Escolar'),
            'failed_disciplines' => AuditedField::list('Disciplinas com Reprovação', model: Discipline::class),
            'at_risk_disciplines' => AuditedField::list('Disciplinas com Risco de Insucesso Acadêmico', model: Discipline::class),
        ];
    }

    public function auditDescription(): string
    {
        $studentName = $this->student?->person?->name ?? "#{$this->student_id}";
        $courseName = $this->course?->name ?? "#{$this->course_id}";

        return "a matrícula de {$studentName} em {$courseName}";
    }

    /** @return array<string, mixed> */
    public function auditExtraValues(): array
    {
        return $this->exists
            ? [
                'failed_disciplines' => $this->failedDisciplines()->pluck('disciplines.id')->all(),
                'at_risk_disciplines' => $this->atRiskDisciplines()->pluck('disciplines.id')->all(),
            ]
            : [];
    }

    /**
     * @throws InvalidStudentCourse
     */
    public static function register(Student $student, Course $course, CreateStudentCourseDTO $data): self
    {
        self::ensurePersisted($student, $course);

        return new self([
            'student_id' => $student->getKey(),
            'course_id' => $course->getKey(),
            'academic_year' => $data->academicYear,
            'is_current' => $data->isCurrent,
            'school_attendance_status' => self::nullableText($data->schoolAttendanceStatus),
        ]);
    }

    /**
     * @throws InvalidStudentCourse
     */
    public function revise(Course $course, UpdateStudentCourseDTO $data): void
    {
        if (! $course->exists) {
            throw new InvalidStudentCourse('A matrícula deve ser vinculada a um curso persistido.');
        }

        $this->fill([
            'course_id' => $course->getKey(),
            'academic_year' => $data->academicYear,
            'school_attendance_status' => self::nullableText($data->schoolAttendanceStatus),
        ]);
    }

    public function markAsNotCurrent(): void
    {
        $this->fill(['is_current' => false]);
    }

    public function markAsCurrent(): void
    {
        $this->fill(['is_current' => true]);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function failedDisciplines(): BelongsToMany
    {
        return $this->disciplinesByCategory(StudentCourseDisciplineCategory::FAILED);
    }

    public function atRiskDisciplines(): BelongsToMany
    {
        return $this->disciplinesByCategory(StudentCourseDisciplineCategory::AT_ACADEMIC_RISK);
    }

    /** @param list<int> $disciplineIds */
    public function syncFailedDisciplines(array $disciplineIds): void
    {
        $before = $this->failedDisciplines()->pluck('disciplines.id')->all();
        $this->failedDisciplines()->sync(array_values(array_unique($disciplineIds)));
        $this->recordAuditFieldChange('failed_disciplines', $before, $this->failedDisciplines()->pluck('disciplines.id')->all());
    }

    /** @param list<int> $disciplineIds */
    public function syncAtRiskDisciplines(array $disciplineIds): void
    {
        $before = $this->atRiskDisciplines()->pluck('disciplines.id')->all();
        $this->atRiskDisciplines()->sync(array_values(array_unique($disciplineIds)));
        $this->recordAuditFieldChange('at_risk_disciplines', $before, $this->atRiskDisciplines()->pluck('disciplines.id')->all());
    }

    /**
     * @param  list<int>  $failedDisciplineIds
     * @param  list<int>  $atRiskDisciplineIds
     *
     * @throws InvalidStudentCourse
     */
    public function syncDisciplines(Course $course, array $failedDisciplineIds, array $atRiskDisciplineIds): void
    {
        $allowedIds = $course->disciplines()
            ->pluck('disciplines.id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        if (array_diff(array_map('intval', [...$failedDisciplineIds, ...$atRiskDisciplineIds]), $allowedIds) !== []) {
            throw new InvalidStudentCourse('As disciplinas selecionadas devem pertencer ao curso do aluno.');
        }

        $this->syncFailedDisciplines($failedDisciplineIds);
        $this->syncAtRiskDisciplines($atRiskDisciplineIds);
    }

    public function getFailedDisciplineNamesAttribute(): string
    {
        return $this->failedDisciplines->pluck('name')->join(', ');
    }

    public function getAtRiskDisciplineNamesAttribute(): string
    {
        return $this->atRiskDisciplines->pluck('name')->join(', ');
    }

    /**
     * @throws InvalidStudentCourse
     */
    private static function ensurePersisted(Student $student, Course $course): void
    {
        if (! $student->exists) {
            throw new InvalidStudentCourse('A matrícula deve ser vinculada a um aluno persistido.');
        }

        if (! $course->exists) {
            throw new InvalidStudentCourse('A matrícula deve ser vinculada a um curso persistido.');
        }
    }

    private function disciplinesByCategory(StudentCourseDisciplineCategory $category): BelongsToMany
    {
        return $this->belongsToMany(
            Discipline::class,
            'student_course_disciplines',
            'student_course_id',
            'discipline_id',
        )
            ->withPivotValue('category', $category->value)
            ->withTimestamps();
    }

    private static function nullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
