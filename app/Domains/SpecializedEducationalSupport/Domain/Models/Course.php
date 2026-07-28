<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Courses\CreateCourseDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Courses\UpdateCourseDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidCourse;
use Database\Factories\Domains\SpecializedEducationalSupport\CourseFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(CourseFactory::class)]
final class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function register(CreateCourseDTO $data): self
    {
        return new self([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'is_active' => $data->isActive,
        ]);
    }

    public function revise(UpdateCourseDTO $data): void
    {
        $this->fill([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'is_active' => $data->isActive,
        ]);
    }

    /**
     * @param  list<int>  $disciplineIds
     *
     * @throws InvalidCourse
     */
    public function assignCurriculum(array $disciplineIds): void
    {
        if (! $this->exists) {
            throw new InvalidCourse(
                'A matriz curricular só pode ser atribuída a um curso persistido.'
            );
        }

        $this->disciplines()->sync(array_values(array_unique($disciplineIds)));
    }

    /**
     * @throws InvalidCourse
     */
    public function ensureCanBeDeactivated(bool $hasStudents): void
    {
        if ($hasStudents) {
            throw new InvalidCourse(
                'Este curso está vinculado a um ou mais alunos e não pode ser desativado.'
            );
        }
    }

    /**
     * @throws InvalidCourse
     */
    public function ensureCanBeDeleted(bool $hasStudents): void
    {
        if ($hasStudents) {
            throw new InvalidCourse(
                'Este curso está vinculado a um ou mais alunos e não pode ser excluído.'
            );
        }
    }

    /**
     * @throws InvalidCourse
     */
    public function ensureIsActive(): void
    {
        if (! $this->is_active) {
            throw new InvalidCourse(
                'Este curso está desativado e não pode ser vinculado.'
            );
        }
    }

    public function studentCourses(): HasMany
    {
        return $this->hasMany(StudentCourse::class, 'course_id');
    }

    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(
            Discipline::class,
            'course_disciplines',
            'course_id',
            'discipline_id',
        )->withTimestamps();
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
