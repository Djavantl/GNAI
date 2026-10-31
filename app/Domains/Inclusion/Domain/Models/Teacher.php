<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\DTOs\Teachers\CreateTeacherDTO;
use App\Domains\Inclusion\Domain\DTOs\Teachers\UpdateTeacherDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidTeacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Teacher extends Model implements Auditable
{
    use AuditsChanges;

    protected $fillable = [
        'person_id',
        'registration',
    ];

    public static function auditType(): string
    {
        return 'teacher';
    }

    public static function auditLabel(): string
    {
        return 'Professor';
    }

    public static function auditedFields(): array
    {
        return [
            'person_id' => AuditedField::reference('Pessoa', Person::class),
            'registration' => AuditedField::text('Matrícula'),
        ];
    }

    public function auditDescription(): string
    {
        return 'o professor '.($this->person?->name ?? "#{$this->getKey()}");
    }

    public static function register(CreateTeacherDTO $data): self
    {
        return new self([
            'person_id' => $data->personId,
            'registration' => $data->registration->value(),
        ]);
    }

    public function revise(UpdateTeacherDTO $data): void
    {
        $this->fill([
            'registration' => $data->registration->value(),
        ]);
    }

    /**
     * Sincroniza as disciplinas do professor por curso, incluindo e removendo
     * um registro por vez para que cada alteração entre na auditoria.
     *
     * @param  array<int|string, list<int|string>>  $assignments
     *
     * @throws InvalidTeacher
     */
    public function assignCourseDisciplines(array $assignments): void
    {
        $assignments = self::normalizeAssignments($assignments);
        $courseIds = array_keys($assignments);
        $courses = Course::query()
            ->whereIn('id', $courseIds)
            ->get()
            ->keyBy('id');

        if ($courses->count() !== count($courseIds)) {
            throw new InvalidTeacher('Um ou mais cursos informados não foram encontrados.');
        }

        $rows = [];

        foreach ($assignments as $courseId => $disciplineIds) {
            /** @var Course $course */
            $course = $courses->get($courseId);
            $course->ensureIsActive();

            $activeDisciplineIds = $course->disciplines()
                ->whereIn('disciplines.id', $disciplineIds)
                ->where('disciplines.is_active', true)
                ->pluck('disciplines.id')
                ->map(fn (mixed $id): int => (int) $id)
                ->all();

            if (count($activeDisciplineIds) !== count($disciplineIds)) {
                throw new InvalidTeacher(
                    "Uma ou mais disciplinas informadas não pertencem ao curso {$course->name} ou estão inativas."
                );
            }

            foreach ($activeDisciplineIds as $disciplineId) {
                $rows["{$course->id}:{$disciplineId}"] = [
                    'teacher_id' => $this->id,
                    'course_id' => $course->id,
                    'discipline_id' => $disciplineId,
                ];
            }
        }

        TeacherCourseDiscipline::query()
            ->where('teacher_id', $this->id)
            ->get()
            ->each(function (TeacherCourseDiscipline $assignment) use (&$rows): void {
                $key = "{$assignment->course_id}:{$assignment->discipline_id}";

                if (isset($rows[$key])) {
                    unset($rows[$key]);

                    return;
                }

                $assignment->delete();
            });

        foreach ($rows as $row) {
            TeacherCourseDiscipline::query()->create($row);
        }
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'teacher_id');
    }

    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(
            Discipline::class,
            'teacher_course_disciplines',
            'teacher_id',
            'discipline_id',
        );
    }

    public function courseDisciplines(): HasMany
    {
        return $this->hasMany(TeacherCourseDiscipline::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'teacher_courses')->withTimestamps();
    }

    public function peiDisciplines(): HasMany
    {
        return $this->hasMany(PeiDiscipline::class, 'teacher_id');
    }

    /**
     * @param  array<int|string, list<int|string>>  $assignments
     * @return array<int, list<int>>
     */
    private static function normalizeAssignments(array $assignments): array
    {
        $normalized = [];

        foreach ($assignments as $courseId => $disciplineIds) {
            $courseId = (int) $courseId;
            $disciplineIds = array_values(array_unique(array_map('intval', (array) $disciplineIds)));

            if ($courseId <= 0 || $disciplineIds === []) {
                continue;
            }

            $normalized[$courseId] = $disciplineIds;
        }

        return $normalized;
    }
}
