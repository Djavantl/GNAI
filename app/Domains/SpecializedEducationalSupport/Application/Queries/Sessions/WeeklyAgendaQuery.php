<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions;

use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\ListSessionsData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Carbon\Carbon;

final class WeeklyAgendaQuery
{
    /**
     * @return array<string, mixed>
     */
    public function execute(ListSessionsData $filters, ?int $fixedProfessionalId = null): array
    {
        $referenceDate = Carbon::parse($filters->week ?? now()->toDateString());
        $weekStart = $referenceDate->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weekEnd = $referenceDate->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $sessions = Session::query()
            ->with(['students.person', 'professional.person', 'sessionRecord', 'pedagogicalRecord'])
            ->whereBetween('session_date', [$weekStart, $weekEnd])
            ->where(function ($query) use ($filters, $fixedProfessionalId): void {
                if ($fixedProfessionalId !== null) {
                    $query->where('professional_id', $fixedProfessionalId);
                } elseif ($filters->professionalAgenda !== null) {
                    $query->where('professional_id', $filters->professionalAgenda);
                }
            })
            ->when($filters->studentAgenda, function ($query, int $studentId): void {
                $query->whereHas('students', fn ($studentQuery) => $studentQuery->where('students.id', $studentId));
            })
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        $days = [];
        $currentDate = $weekStart->copy();

        while ($currentDate <= $weekEnd) {
            $dateString = $currentDate->toDateString();

            $days[] = [
                'date' => $currentDate->copy(),
                'label' => $this->translatedDayName($currentDate),
                'sessions' => $sessions
                    ->filter(fn (Session $session): bool => $session->session_date->toDateString() === $dateString)
                    ->values(),
            ];

            $currentDate->addDay();
        }

        return [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'days' => $days,
        ];
    }

    private function translatedDayName(Carbon $date): string
    {
        return [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
        ][$date->dayOfWeek];
    }
}
