<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Students;

use App\Domains\SpecializedEducationalSupport\Application\Data\Students\ListStudentsData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class ListStudentsQuery
{
    /**
     * @return LengthAwarePaginator<int, Student>
     */
    public function execute(ListStudentsData $filters, ?int $teacherId = null): LengthAwarePaginator
    {
        $query = Student::query()
            ->select('students.*')
            ->join('people', 'people.id', '=', 'students.person_id')
            ->with(['person', 'currentCourse.course']);

        if ($teacherId !== null) {
            $query->whereHas('courses', function (Builder $courseQuery) use ($teacherId): void {
                $courseQuery->whereIn('courses.id', function ($teacherCoursesQuery) use ($teacherId): void {
                    $teacherCoursesQuery
                        ->select('course_id')
                        ->from('teacher_courses')
                        ->where('teacher_id', $teacherId);
                });
            });
        }

        if (filled($filters->name)) {
            $query->where('people.name', 'like', trim($filters->name).'%');
        }

        if (filled($filters->phone)) {
            $query->where('people.phone', 'like', '%'.$filters->phone.'%');
        }

        if (filled($filters->email)) {
            $query->where('people.email', 'like', '%'.trim($filters->email).'%');
        }

        if (filled($filters->registration)) {
            $query->where('students.registration', 'like', '%'.trim($filters->registration).'%');
        }

        if ($filters->status !== null) {
            $query->where('students.status', $filters->status->value);
        }

        if ($filters->courseId === 0) {
            $query->whereDoesntHave('currentCourse');
        } elseif ($filters->courseId !== null) {
            $query->whereHas(
                'currentCourse',
                fn (Builder $courseQuery): Builder => $courseQuery->where('course_id', $filters->courseId),
            );
        }

        if ($filters->deficiencyId === 0) {
            $query->whereDoesntHave('deficiencies');
        } elseif ($filters->deficiencyId !== null) {
            $query->whereHas(
                'deficiencies',
                fn (Builder $deficiencyQuery): Builder => $deficiencyQuery->where('deficiencies.id', $filters->deficiencyId),
            );
        }

        return $query
            ->orderBy('people.name')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
