<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Presenters;

use App\Domains\Inclusion\Application\Data\Sessions\ListSessionsData;
use App\Domains\Inclusion\Domain\Enums\AttendanceType;
use App\Domains\Inclusion\Domain\Enums\SessionStatus;
use App\Domains\Inclusion\Domain\Enums\SessionType;
use App\Domains\Inclusion\Domain\Models\Session;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as ConcreteLengthAwarePaginator;
use Illuminate\Support\Collection;

final class SessionListPresenter
{
    public function sessions(LengthAwarePaginator $sessions, Request $request): LengthAwarePaginator
    {
        $userId = (int) $request->user()?->getAuthIdentifier();
        $canDelete = $request->user()?->can('session.delete') ?? false;

        /** @var ConcreteLengthAwarePaginator $sessions */
        return $sessions->through(
            fn (Session $session): array => $this->row($session, $userId, $canDelete),
        );
    }

    /**
     * @param  array{rangeStart: CarbonInterface, rangeEnd: CarbonInterface, sessions: Collection<int, Session>}  $calendar
     * @return array<string, mixed>
     */
    public function calendar(array $calendar, Request $request): array
    {
        $userId = (int) $request->user()?->getAuthIdentifier();
        $canDelete = $request->user()?->can('session.delete') ?? false;
        $sessions = $calendar['sessions']
            ->map(fn (Session $session): array => $this->row($session, $userId, $canDelete))
            ->values();

        return [
            'rangeStart' => $calendar['rangeStart']->toDateString(),
            'rangeEnd' => $calendar['rangeEnd']->toDateString(),
            'total' => $sessions->count(),
            'sessions' => $sessions->all(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function filters(ListSessionsData $filters): array
    {
        return [
            'student' => $filters->student === null ? '' : (string) $filters->student,
            'professional' => $filters->professional === null ? '' : (string) $filters->professional,
            'type' => $filters->type ?? '',
            'status' => $filters->status ?? '',
            'week' => $filters->week ?? now()->toDateString(),
            'student_agenda' => $filters->studentAgenda === null ? '' : (string) $filters->studentAgenda,
            'professional_agenda' => $filters->professionalAgenda === null ? '' : (string) $filters->professionalAgenda,
        ];
    }

    /**
     * @param  iterable<int, mixed>  $people
     * @return list<array{value: string, label: string}>
     */
    public function peopleOptions(iterable $people, string $fallback): array
    {
        return collect($people)
            ->map(fn ($item): array => [
                'value' => (string) $item->id,
                'label' => $item->person?->name ?? $fallback,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{types: list<array{value: string, label: string}>, statuses: list<array{value: string, label: string}>}
     */
    public function enumOptions(): array
    {
        $map = fn (array $options): array => collect($options)
            ->map(fn (string $label, string $value): array => ['value' => $value, 'label' => $label])
            ->values()
            ->all();

        return [
            'types' => $map(SessionType::options()),
            'statuses' => $map(SessionStatus::options()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function row(Session $session, int $userId, bool $canDelete): array
    {
        $isPedagogical = AttendanceType::isPedagogical($session->attendance_type);
        $hasRecord = $isPedagogical
            ? (bool) $session->pedagogicalRecord
            : (bool) $session->aeeRecord;

        return [
            'id' => $session->id,
            'session_date' => $session->session_date?->toDateString(),
            'start_time' => $this->time($session->start_time),
            'end_time' => $this->time($session->end_time),
            'type_label' => SessionType::labelFor($session->type),
            'status_label' => SessionStatus::labelFor($session->status),
            'status_color' => SessionStatus::colorFor($session->status),
            'is_scheduled' => SessionStatus::isScheduledValue($session->status),
            'professional_name' => $session->professional?->person?->name ?? 'Profissional',
            'student_names' => $session->students
                ->map(fn ($student): string => $student->person?->name ?? 'Aluno')
                ->values()
                ->all(),
            'has_record' => $hasRecord,
            'record_label' => $hasRecord
                ? ($isPedagogical ? 'Atendimento Pedagógico registrado' : 'Atendimento AEE registrado')
                : 'Sem registro',
            // Somente quem criou o agendamento pode excluí-lo (DeleteSessionAction).
            'can_delete' => $canDelete && (int) $session->creator_id === $userId,
        ];
    }

    private function time(mixed $value): ?string
    {
        return $value === null || $value === '' ? null : Carbon::parse($value)->format('H:i');
    }
}
