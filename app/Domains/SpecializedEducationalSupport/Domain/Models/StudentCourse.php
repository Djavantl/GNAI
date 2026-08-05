<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentCourses\CreateStudentCourseDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentCourses\UpdateStudentCourseDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentCourseDisciplineCategory;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentCourse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

final class StudentCourse extends Pivot
{
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
            'is_current' => $data->isCurrent,
            'school_attendance_status' => self::nullableText($data->schoolAttendanceStatus),
        ]);
    }

    public function markAsNotCurrent(): void
    {
        $this->fill(['is_current' => false]);
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
        $this->failedDisciplines()->sync(array_values(array_unique($disciplineIds)));
    }

    /** @param list<int> $disciplineIds */
    public function syncAtRiskDisciplines(array $disciplineIds): void
    {
        $this->atRiskDisciplines()->sync(array_values(array_unique($disciplineIds)));
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
