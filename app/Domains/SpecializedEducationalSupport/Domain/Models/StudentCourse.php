<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentCourses\CreateStudentCourseDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentCourses\UpdateStudentCourseDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentCourse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
}
