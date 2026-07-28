<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentCourses;

use App\Domains\SpecializedEducationalSupport\Application\Data\StudentCourses\ListStudentCoursesData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListStudentCoursesQuery
{
    /**
     * @return LengthAwarePaginator<int, StudentCourse>
     */
    public function execute(Student $student, ListStudentCoursesData $filters): LengthAwarePaginator
    {
        return StudentCourse::query()
            ->with('course')
            ->where('student_id', $student->getKey())
            ->when($filters->courseId !== null, static function ($query) use ($filters): void {
                $query->where('course_id', $filters->courseId);
            })
            ->when(filled($filters->academicYear), static function ($query) use ($filters): void {
                $query->where('academic_year', 'like', '%'.trim((string) $filters->academicYear).'%');
            })
            ->when($filters->isCurrent !== null, static function ($query) use ($filters): void {
                $query->where('is_current', $filters->isCurrent);
            })
            ->orderByDesc('academic_year')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
