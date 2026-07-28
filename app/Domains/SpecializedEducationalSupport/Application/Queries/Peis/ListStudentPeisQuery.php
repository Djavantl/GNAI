<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Peis;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\Peis\ListPeisData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListStudentPeisQuery
{
    public function execute(Student $student, ListPeisData $filters, ?User $user): LengthAwarePaginator
    {
        $query = Pei::query()
            ->where('student_id', $student->getKey())
            ->with(['student.person', 'semester', 'course']);

        if ($user?->teacher_id !== null) {
            $teacherCourseIds = $user->teacher
                ?->courses()
                ->pluck('courses.id') ?? collect();

            $query->whereIn('course_id', $teacherCourseIds);
        }

        if ($filters->semesterId !== null) {
            $query->where('semester_id', $filters->semesterId);
        }

        if ($filters->isFinished !== null && $filters->isFinished !== '') {
            $query->where('is_finished', filter_var($filters->isFinished, FILTER_VALIDATE_BOOLEAN));
        }

        if ($filters->version !== null) {
            $query->where('version', $filters->version);
        }

        return $query
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }
}
