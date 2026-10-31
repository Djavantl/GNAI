<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Inclusion\Domain\DTOs\Disciplines\CreateDisciplineDTO;
use App\Domains\Inclusion\Domain\DTOs\Disciplines\UpdateDisciplineDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidDiscipline;
use Database\Factories\Domains\Inclusion\DisciplineFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[UseFactory(DisciplineFactory::class)]
final class Discipline extends Model implements Auditable
{
    use AuditsChanges;
    use HasFactory;

    protected $table = 'disciplines';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function auditType(): string
    {
        return 'discipline';
    }

    public static function auditLabel(): string
    {
        return 'Disciplina';
    }

    public static function auditedFields(): array
    {
        return [
            'name' => AuditedField::text('Nome da Disciplina'),
            'description' => AuditedField::richText('Descrição'),
            'is_active' => AuditedField::boolean('Ativa'),
        ];
    }

    public function auditDescription(): string
    {
        return "a disciplina {$this->name}";
    }

    public static function register(CreateDisciplineDTO $data): self
    {
        return new self([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'is_active' => $data->isActive,
        ]);
    }

    public function revise(UpdateDisciplineDTO $data): void
    {
        $this->fill([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'is_active' => $data->isActive,
        ]);
    }

    /**
     * @throws InvalidDiscipline
     */
    public function ensureCanBeDeactivated(bool $hasCourses, bool $hasTeachers): void
    {
        if ($hasCourses) {
            throw new InvalidDiscipline(
                "Não é possível inativar a disciplina '{$this->name}' pois ela está vinculada a um ou mais cursos."
            );
        }

        if ($hasTeachers) {
            throw new InvalidDiscipline(
                "Não é possível inativar a disciplina '{$this->name}' pois ela está atribuída a professores."
            );
        }
    }

    /**
     * @throws InvalidDiscipline
     */
    public function ensureCanBeDeleted(bool $hasTeachers, bool $hasCourses, bool $hasStudentCourseRecords): void
    {
        if ($hasStudentCourseRecords) {
            throw new InvalidDiscipline(
                "Não é possível excluir a disciplina '{$this->name}' pois ela possui registros no histórico de cursos de alunos."
            );
        }

        if ($hasTeachers) {
            throw new InvalidDiscipline(
                "Não é possível excluir a disciplina '{$this->name}' pois ela está vinculada a professores."
            );
        }

        if ($hasCourses) {
            throw new InvalidDiscipline(
                "Não é possível excluir a disciplina '{$this->name}' pois ela está vinculada a cursos."
            );
        }

    }

    /**
     * @throws InvalidDiscipline
     */
    public function ensureIsActive(): void
    {
        if (! $this->is_active) {
            throw new InvalidDiscipline(
                "A disciplina '{$this->name}' está inativa e não pode ser utilizada."
            );
        }
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_disciplines',
            'discipline_id',
            'course_id',
        )->withTimestamps();
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            Teacher::class,
            'teacher_course_disciplines',
            'discipline_id',
            'teacher_id',
        );
    }

    public function studentCoursesWithAcademicRecords(): BelongsToMany
    {
        return $this->belongsToMany(
            StudentCourse::class,
            'student_course_disciplines',
            'discipline_id',
            'student_course_id',
        )
            ->withPivot('category')
            ->withTimestamps();
    }

    private static function normalizeName(string $name): string
    {
        return trim($name);
    }

    private static function normalizeDescription(?string $description): ?string
    {
        $description = trim((string) $description);

        return $description === '' ? null : $description;
    }
}
