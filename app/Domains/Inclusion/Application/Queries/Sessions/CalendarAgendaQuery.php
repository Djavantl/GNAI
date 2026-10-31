<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Sessions;

use App\Domains\Inclusion\Application\Data\Sessions\ListSessionsData;
use App\Domains\Inclusion\Domain\Models\Session;
use Carbon\Carbon;

final class CalendarAgendaQuery
{
    /**
     * @return array<string, mixed>
     */
    public function execute(ListSessionsData $filters, ?int $professionalId = null): array
    {
        $referenceDate = Carbon::parse($filters->week ?? now()->toDateString());
        $rangeStart = $referenceDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY)->startOfDay();
        $rangeEnd = $referenceDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY)->endOfDay();

        $sessions = Session::query()
            ->with(['students.person', 'professional.person', 'aeeRecord', 'pedagogicalRecord'])
            ->whereBetween('session_date', [$rangeStart, $rangeEnd])
            ->when($filters->student !== null, function ($query) use ($filters): void {
                $query->whereHas('students', fn ($studentQuery) => $studentQuery->where('students.id', $filters->student));
            })
            ->where(function ($query) use ($filters, $professionalId): void {
                if ($professionalId !== null) {
                    $query->where('professional_id', $professionalId);
                } elseif ($filters->professional !== null) {
                    $query->where('professional_id', $filters->professional);
                }
            })
            ->when($filters->type !== null && $filters->type !== '', fn ($query) => $query->where('type', $filters->type))
            ->when($filters->status !== null && $filters->status !== '', fn ($query) => $query->where('status', $filters->status))
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        return [
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'sessions' => $sessions,
        ];
    }
}
