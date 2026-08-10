<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\PedagogicalRecords;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords\ListPedagogicalRecordsData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PedagogicalRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListPedagogicalRecordsQuery
{
    public function execute(ListPedagogicalRecordsData $filters, User $user, bool $onlyOwn = false, ?Student $student = null): LengthAwarePaginator
    {
        return PedagogicalRecord::query()
            ->with([
                'attendanceSession.students.person',
                'attendanceSession.students.currentCourse.course',
                'attendanceSession.professional.person',
                'guardians.person',
            ])
            ->when($onlyOwn || ! $user->can('pedagogical-record.view-all'), fn ($query) => $query->whereHas('attendanceSession', fn ($session) => $session->where('professional_id', $user->professional_id ?? 0)))
            ->when($student !== null, fn ($query) => $query->whereHas('attendanceSession.students', fn ($students) => $students->where('students.id', $student->getKey())))
            ->when($student === null && $filters->student !== null, fn ($query) => $query->whereHas('attendanceSession.students', fn ($students) => $students->where('students.id', $filters->student)))
            ->when($filters->professionalId !== null, fn ($query) => $query->whereHas('attendanceSession', fn ($session) => $session->where('professional_id', $filters->professionalId)))
            ->when($filters->isPresent !== null, fn ($query) => $query->where('is_present', $filters->isPresent))
            ->when($filters->withGuardians === true, fn ($query) => $query->whereHas('guardians'))
            ->when($filters->withGuardians === false, fn ($query) => $query->whereDoesntHave('guardians'))
            ->when($filters->courseId === 0, fn ($query) => $query->whereHas('attendanceSession.students', fn ($students) => $students->whereDoesntHave('currentCourse')))
            ->when($filters->courseId !== null && $filters->courseId > 0, fn ($query) => $query->whereHas('attendanceSession.students.currentCourse', fn ($course) => $course->where('course_id', $filters->courseId)))
            ->orderByDesc(
                Session::query()
                    ->select('session_date')
                    ->whereColumn('attendance_sessions.id', 'pedagogical_records.attendance_session_id')
                    ->limit(1)
            )
            ->orderByDesc('id')
            ->paginate($filters->perPage, ['*'], $student === null ? 'page' : 'pedagogical_page')
            ->withQueryString();
    }
}
