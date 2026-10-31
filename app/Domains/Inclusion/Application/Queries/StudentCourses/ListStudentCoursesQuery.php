<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\StudentCourses;

use App\Domains\Inclusion\Application\Data\StudentCourses\ListStudentCoursesData;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentCourse;
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
            ->orderByDesc('is_current')
            ->orderByDesc('academic_year')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
