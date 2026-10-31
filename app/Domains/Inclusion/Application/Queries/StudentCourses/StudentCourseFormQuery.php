<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\StudentCourses;

use App\Domains\Inclusion\Domain\Exceptions\InvalidStudent;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStudentCourse;
use App\Domains\Inclusion\Domain\Models\Course;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentCourse;
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
