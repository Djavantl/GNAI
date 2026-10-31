<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Sessions;

use App\Domains\Inclusion\Domain\Enums\AttendanceType;
use App\Domains\Inclusion\Domain\Enums\SessionType;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\Models\Session;
use App\Domains\Inclusion\Domain\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class SessionFormQuery
{
    /**
     * @return array<string, mixed>
     */
    public function forCreation(): array
    {
        return $this->formOptions() + [
            'defaults' => [
                'attendance_type' => AttendanceType::AEE->value,
                'type' => SessionType::INDIVIDUAL->value,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forUpdate(Session $session): array
    {
        return $this->timeOptions() + $this->typeOptions() + [
            'session' => $session->load(['students.person', 'professional.person', 'aeeRecord', 'pedagogicalRecord']),
            'sessionForm' => [
                'attendance_type' => AttendanceType::valueOf($session->attendance_type),
                'session_date' => $session->session_date?->format('Y-m-d') ?? '',
                'start_time' => $session->start_time ? Carbon::parse($session->start_time)->format('H:i') : '',
                'end_time' => $session->end_time ? Carbon::parse($session->end_time)->format('H:i') : '',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return $this->timeOptions() + $this->typeOptions() + [
            'students' => $this->students(),
            'professionals' => $this->professionals(),
        ];
    }

    /**
     * @return array{attendanceTypes: array<string, string>, sessionTypes: array<string, string>}
     */
    private function typeOptions(): array
    {
        return [
            'attendanceTypes' => AttendanceType::options(),
            'sessionTypes' => SessionType::options(),
        ];
    }

    /**
     * @return array{startTimes: array<string, string>, endTimes: array<string, string>}
     */
    private function timeOptions(): array
    {
        $periods = [
            ['start' => '08:00', 'end' => '12:00'],
            ['start' => '14:00', 'end' => '17:00'],
        ];

        $startTimes = [];
        $endTimes = [];

        foreach ($periods as $period) {
            $current = Carbon::parse($period['start']);
            $end = Carbon::parse($period['end']);

            while ($current <= $end) {
                $time = $current->format('H:i');

                if ($time !== '12:00' && $time !== '17:00') {
                    $startTimes[$time] = $time;
                }

                if ($time !== '08:00' && $time !== '14:00') {
                    $endTimes[$time] = $time;
                }

                $current->addMinutes(30);
            }
        }

        return [
            'startTimes' => $startTimes,
            'endTimes' => $endTimes,
        ];
    }

    /**
     * @return Collection<int, Student>
     */
    private function students(): Collection
    {
        return Student::query()
            ->with('person')
            ->join('people', 'people.id', '=', 'students.person_id')
            ->select('students.*')
            ->orderBy('people.name')
            ->get();
    }

    /**
     * @return Collection<int, Professional>
     */
    private function professionals(): Collection
    {
        return Professional::query()
            ->with('person')
            ->join('people', 'people.id', '=', 'professionals.person_id')
            ->select('professionals.*')
            ->orderBy('people.name')
            ->get();
    }
}
