<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentCourses;

use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudent;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentCourse;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use Illuminate\Database\Eloquent\Collection;

final class StudentCourseFormQuery
{
    /**
     * @return array<string, mixed>
     *
     * @throws InvalidStudent
     */
    public function forCreation(Student $student): array
    {
        $student->ensureIsActive();
        $student->loadMissing('person');
        $mustBeCurrent = ! $student->studentCourses()->exists();

        return [
            'student' => $student,
            'courses' => $this->activeCourses(),
            'mustBeCurrent' => $mustBeCurrent,
        ];
    }

    /**
     * @return array<string, mixed>
     *
     * @throws InvalidStudent
     */
    public function forUpdate(StudentCourse $studentCourse): array
    {
        $studentCourse->loadMissing(['student.person', 'course.disciplines', 'failedDisciplines', 'atRiskDisciplines']);
        $studentCourse->student->ensureIsActive();
        if (! $studentCourse->is_current) {
            throw new InvalidStudentCourse('Cursos anteriores são históricos e não podem mais ser editados.');
        }

        return [
            'studentCourse' => $studentCourse,
            'courses' => $this->activeCourses(),
        ];
    }

    /**
     * @return Collection<int, Course>
     */
    private function activeCourses(): Collection
    {
        return Course::query()
            ->with(['disciplines' => static fn ($query) => $query->orderBy('name')])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
