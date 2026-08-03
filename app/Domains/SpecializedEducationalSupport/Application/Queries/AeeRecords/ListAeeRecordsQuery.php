<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\AeeRecords;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\ListAeeRecordsData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListAeeRecordsQuery
{
    public function execute(ListAeeRecordsData $filters, User $user, bool $onlyOwn = false): LengthAwarePaginator
    {
        return AeeRecord::query()
            ->with([
                'attendanceSession.professional.person',
                'studentEvaluations.student.person',
            ])
            ->when($onlyOwn || ! $user->can('aee-record.view-all'), fn ($query) => $query->whereHas('attendanceSession', fn ($session) => $session->where('professional_id', $user->professional_id ?? 0)))
            ->when($filters->student !== null, fn ($query) => $query->whereHas('studentEvaluations', fn ($evaluation) => $evaluation->where('student_id', $filters->student)))
            ->orderByDesc('id')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
