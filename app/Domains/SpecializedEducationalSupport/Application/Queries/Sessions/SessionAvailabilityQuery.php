<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions;

use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\SessionAvailabilityData;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Carbon\Carbon;

final class SessionAvailabilityQuery
{
    /**
     * @return array{slots: list<array{time: string, busy: bool, busy_type: string}>}
     */
    public function execute(SessionAvailabilityData $data): array
    {
        if ($data->date === null || $data->professional === null || $data->studentIds === []) {
            return ['slots' => []];
        }

        $sessions = Session::query()
            ->whereDate('session_date', $data->date)
            ->where(function ($query) use ($data): void {
                $query->where('professional_id', $data->professional)
                    ->orWhereHas('students', fn ($studentQuery) => $studentQuery->whereIn('students.id', $data->studentIds));
            })
            ->where(function ($query): void {
                $query->whereNull('status')
                    ->orWhereRaw('LOWER(status) <> ?', [mb_strtolower(SessionStatus::CANCELLED_DATABASE_VALUE)]);
            })
            ->with(['students.person', 'professional.person'])
            ->get();

        $slots = [];
        $periods = [
            ['start' => '08:00', 'end' => '12:00'],
            ['start' => '14:00', 'end' => '17:00'],
        ];

        foreach ($periods as $period) {
            $time = Carbon::parse($period['start']);
            $endTime = Carbon::parse($period['end']);

            while ($time < $endTime) {
                $slotStart = $time->copy();
                $slotEnd = $time->copy()->addMinutes(30);
                $occupants = [];

                foreach ($sessions as $session) {
                    $sessionStart = Carbon::parse($session->start_time);
                    $sessionEnd = Carbon::parse($session->end_time);

                    if (! ($sessionStart < $slotEnd && $sessionEnd > $slotStart)) {
                        continue;
                    }

                    if ((int) $session->professional_id === $data->professional) {
                        $occupants[] = 'Profissional';
                    }

                    foreach ($session->students->whereIn('id', $data->studentIds) as $student) {
                        $occupants[] = explode(' ', $student->person->name)[0];
                    }
                }

                $occupants = array_values(array_unique($occupants));

                $slots[] = [
                    'time' => $slotStart->format('H:i'),
                    'busy' => $occupants !== [],
                    'busy_type' => implode(', ', $occupants),
                ];

                $time->addMinutes(30);
            }
        }

        return ['slots' => $slots];
    }
}
