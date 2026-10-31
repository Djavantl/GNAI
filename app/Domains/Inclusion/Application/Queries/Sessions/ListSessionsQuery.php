<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Sessions;

use App\Domains\Inclusion\Application\Data\Sessions\ListSessionsData;
use App\Domains\Inclusion\Domain\Models\Session;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListSessionsQuery
{
    public function execute(ListSessionsData $filters): LengthAwarePaginator
    {
        $referenceDate = Carbon::parse($filters->week ?? now()->toDateString());

        return Session::query()
            ->with(['students.person', 'professional.person', 'aeeRecord', 'pedagogicalRecord'])
            ->whereBetween('session_date', [
                $referenceDate->copy()->startOfMonth()->startOfDay(),
                $referenceDate->copy()->endOfMonth()->endOfDay(),
            ])
            ->when($filters->student !== null, function ($query) use ($filters): void {
                $query->whereHas('students', fn ($studentQuery) => $studentQuery->where('students.id', $filters->student));
            })
            ->when($filters->professional !== null, fn ($query) => $query->where('professional_id', $filters->professional))
            ->when($filters->type !== null && $filters->type !== '', fn ($query) => $query->where('type', $filters->type))
            ->when($filters->status !== null && $filters->status !== '', fn ($query) => $query->where('status', $filters->status))
            ->orderByDesc('session_date')
            ->paginate(10)
            ->withQueryString();
    }
}
