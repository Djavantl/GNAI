<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\AeeRecords;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\ListAeeRecordsData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListAeeRecordsQuery
{
    public function execute(ListAeeRecordsData $filters, User $user, bool $onlyOwn = false): LengthAwarePaginator
    {
        return AeeRecord::query()
            ->with([
                'attendanceSession.professional.person',
                'studentEvaluations.student.person',
                'studentEvaluations.student.currentCourse.course',
            ])
            ->when($onlyOwn || ! $user->can('aee-record.view-all'), fn ($query) => $query->whereHas('attendanceSession', fn ($session) => $session->where('professional_id', $user->professional_id ?? 0)))
            ->when($filters->student !== null, fn ($query) => $query->whereHas('studentEvaluations', fn ($evaluation) => $evaluation->where('student_id', $filters->student)))
            ->when($filters->professionalId !== null, fn ($query) => $query->whereHas('attendanceSession', fn ($session) => $session->where('professional_id', $filters->professionalId)))
            ->when($filters->isPresent !== null, fn ($query) => $query->whereHas('studentEvaluations', fn ($evaluation) => $evaluation->where('is_present', $filters->isPresent)))
            ->when($filters->courseId === 0, fn ($query) => $query->whereHas('studentEvaluations.student', fn ($student) => $student->whereDoesntHave('currentCourse')))
            ->when($filters->courseId !== null && $filters->courseId > 0, fn ($query) => $query->whereHas('studentEvaluations.student.currentCourse', fn ($course) => $course->where('course_id', $filters->courseId)))
            ->orderByDesc(
                Session::query()
                    ->select('session_date')
                    ->whereColumn('attendance_sessions.id', 'aee_records.attendance_session_id')
                    ->limit(1)
            )
            ->orderByDesc('id')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
