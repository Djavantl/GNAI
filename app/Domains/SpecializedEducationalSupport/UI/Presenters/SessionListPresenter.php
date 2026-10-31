<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Presenters;

use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\ListSessionsData;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionType;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as ConcreteLengthAwarePaginator;
use Illuminate\Support\Collection;

/** Presentation-only values that the legacy session listings computed in Blade. */
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
     * @param  array{weekStart: CarbonInterface, weekEnd: CarbonInterface, days: array<int, array{date: CarbonInterface, label: string, sessions: Collection<int, Session>}>}  $agenda
     * @return array<string, mixed>
     */
    public function agenda(array $agenda, Request $request): array
    {
        $userId = (int) $request->user()?->getAuthIdentifier();
        $canDelete = $request->user()?->can('session.delete') ?? false;
        $days = collect($agenda['days'])->map(fn (array $day): array => [
            'date' => $day['date']->toDateString(),
            'label' => $day['label'],
            'sessions' => $day['sessions']
                ->map(fn (Session $session): array => $this->row($session, $userId, $canDelete))
                ->values()
                ->all(),
        ]);

        return [
            'weekStart' => $agenda['weekStart']->toDateString(),
            'weekEnd' => $agenda['weekEnd']->toDateString(),
            'total' => $days->sum(fn (array $day): int => count($day['sessions'])),
            'days' => $days->all(),
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
            // DeleteSessionAction only accepts the creator; hide the action for other users.
            'can_delete' => $canDelete && (int) $session->creator_id === $userId,
        ];
    }

    private function time(mixed $value): ?string
    {
        return $value === null || $value === '' ? null : Carbon::parse($value)->format('H:i');
    }
}
