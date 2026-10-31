<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\Auth\Application\Resolvers\AuthenticatedUserResolver;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Sessions\CancelSessionAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Sessions\CreateSessionAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Sessions\DeleteSessionAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Sessions\ForceDeleteSessionAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Sessions\RestoreSessionAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Sessions\UpdateSessionAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\CancelSessionData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\CreateSessionData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\ListSessionsData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\SessionAvailabilityData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\UpdateSessionData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions\ListMySessionsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions\ListSessionsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions\ListStudentSessionsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions\SessionAvailabilityQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions\SessionFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions\ShowSessionQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions\WeeklyAgendaQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\UI\Presenters\SessionListPresenter;
use App\Domains\SpecializedEducationalSupport\UI\Presenters\SessionShowPresenter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class SessionController
{
    /**
     * @throws Throwable
     */
    public function index(
        ListSessionsData $filters,
        ListSessionsQuery $query,
        WeeklyAgendaQuery $agendaQuery,
        SessionListPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        $sessions = $query->execute($filters);
        $students = Student::with('person')->orderBy('id')->get();
        $professionals = Professional::with('person')->orderBy('id')->get();
        $agenda = $agendaQuery->execute($filters);
        $weekNavigation = $this->buildWeekNavigation(
            $request,
            'specialized-educational-support.sessions.index',
        );
        $user = $request->user();

        return Inertia::render('SpecializedEducationalSupport/Sessions/Index', [
            'sessions' => $presenter->sessions($sessions, $request),
            'agenda' => $presenter->agenda($agenda, $request),
            'weekNavigation' => $weekNavigation,
            'filters' => $presenter->filters($filters),
            'options' => [
                'students' => $presenter->peopleOptions($students, 'Aluno'),
                'professionals' => $presenter->peopleOptions($professionals, 'Profissional'),
                ...$presenter->enumOptions(),
            ],
            'can' => [
                'create' => $user?->can('session.create') ?? false,
                'view' => $user?->can('session.view') ?? false,
                'mySessions' => $user?->professional !== null && ($user?->can('session.view-own') ?? false),
            ],
        ]);
    }

    public function create(SessionFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Sessions/Create',
            $form->forCreation(),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateSessionData $data, CreateSessionAction $action, Request $request): RedirectResponse
    {
        $session = $action->execute(
            data: $data,
            creatorId: (int) $request->user()?->getAuthIdentifier(),
        );

        return redirect()
            ->route('specialized-educational-support.sessions.show', $session)
            ->with('success', 'Agendamento criado com sucesso.');
    }

    public function show(
        Session $session,
        ShowSessionQuery $query,
        SessionShowPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        $session = $query->execute($session);

        return Inertia::render(
            'SpecializedEducationalSupport/Sessions/Show',
            $presenter->present($session, $request),
        );
    }

    /**
     * @throws Throwable
     */
    public function edit(Session $session, SessionFormQuery $form, Request $request): InertiaResponse
    {
        $userId = (int) $request->user()?->getAuthIdentifier();
        $session->ensureCreatedBy($userId, 'editá-la');
        $session->ensureIsScheduled();

        return Inertia::render(
            'SpecializedEducationalSupport/Sessions/Edit',
            $form->forUpdate($session),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(
        UpdateSessionData $data,
        Session $session,
        UpdateSessionAction $action,
        Request $request,
    ): RedirectResponse {
        $session = $action->execute(
            session: $session,
            data: $data,
            userId: (int) $request->user()?->getAuthIdentifier(),
        );

        return redirect()
            ->route('specialized-educational-support.sessions.show', $session)
            ->with('success', 'Agendamento atualizado com sucesso.');
    }

    public function indexByStudent(
        Student $student,
        ListSessionsData $filters,
        ListStudentSessionsQuery $query,
        SessionListPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        $sessions = $query->execute($student, $filters);
        $professionals = Professional::with('person')
            ->orderBy('id')
            ->get(['id', 'person_id']);
        $student->loadMissing('person');

        return Inertia::render('SpecializedEducationalSupport/Sessions/StudentIndex', [
            'sessions' => $presenter->sessions($sessions, $request),
            'student' => [
                'id' => $student->id,
                'name' => $student->person?->name ?? 'Aluno',
            ],
            'filters' => $presenter->filters($filters),
            'options' => [
                'professionals' => $presenter->peopleOptions($professionals, 'Profissional'),
                ...$presenter->enumOptions(),
            ],
            'can' => [
                'view' => $request->user()?->can('session.view') ?? false,
            ],
        ]);
    }

    /**
     * @throws Throwable
     */
    public function mySessions(
        ListSessionsData $filters,
        ListMySessionsQuery $query,
        WeeklyAgendaQuery $agendaQuery,
        AuthenticatedUserResolver $authenticatedUser,
        SessionListPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        $user = $authenticatedUser->fromRequest($request);

        $sessions = $query->execute($filters, $user);
        $students = Student::with('person')->orderBy('id')->get(['id', 'person_id']);
        $professionalId = $user->professional?->id;
        abort_unless($professionalId !== null, 403, 'Acesso permitido apenas para profissionais.');
        $agenda = $agendaQuery->execute($filters, (int) $professionalId);
        $weekNavigation = $this->buildWeekNavigation(
            $request,
            'specialized-educational-support.sessions.my-sessions',
        );

        return Inertia::render('SpecializedEducationalSupport/Sessions/MySessions', [
            'sessions' => $presenter->sessions($sessions, $request),
            'agenda' => $presenter->agenda($agenda, $request),
            'weekNavigation' => $weekNavigation,
            'filters' => $presenter->filters($filters),
            'options' => [
                'students' => $presenter->peopleOptions($students, 'Aluno'),
                ...$presenter->enumOptions(),
            ],
            'can' => [
                'create' => $user->can('session.create'),
                'view' => $user->can('session.view'),
                'viewAll' => $user->can('session.view-all'),
            ],
        ]);
    }

    /**
     * @throws Throwable
     */
    public function cancel(
        Session $session,
        CancelSessionData $data,
        CancelSessionAction $action,
        Request $request,
    ): RedirectResponse {
        $action->execute(
            session: $session,
            data: $data,
            userId: (int) $request->user()?->getAuthIdentifier(),
        );

        $message = $data->sendNotification
            ? 'Agendamento cancelado e participantes notificados.'
            : 'Agendamento cancelado com sucesso.';

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * @throws Throwable
     */
    public function destroy(Session $session, DeleteSessionAction $action, Request $request): RedirectResponse
    {
        $action->execute(
            session: $session,
            userId: (int) $request->user()?->getAuthIdentifier(),
            sendNotification: $request->boolean('send_notification'),
        );

        return redirect()
            ->route('specialized-educational-support.sessions.index')
            ->with('success', 'Agendamento removido com sucesso.');
    }

    public function restore(Session $session, RestoreSessionAction $action): RedirectResponse
    {
        $action->execute($session);

        return redirect()
            ->route('specialized-educational-support.sessions.index')
            ->with('success', 'Agendamento restaurado com sucesso.');
    }

    public function forceDelete(Session $session, ForceDeleteSessionAction $action): RedirectResponse
    {
        $action->execute($session);

        return redirect()
            ->back()
            ->with('success', 'Removido permanentemente.');
    }

    public function availability(SessionAvailabilityData $data, SessionAvailabilityQuery $query): JsonResponse
    {
        return response()->json($query->execute($data));
    }

    /**
     * @return array{previous: string, current: string, next: string}
     */
    private function buildWeekNavigation(Request $request, string $routeName): array
    {
        $referenceWeek = Carbon::parse($request->input('week', now()->toDateString()))
            ->startOfWeek(Carbon::MONDAY);

        $baseParams = collect($request->except(['week', 'page']))
            ->filter(fn ($value): bool => $value !== null && $value !== '')
            ->toArray();

        return [
            'previous' => route($routeName, array_merge($baseParams, [
                'week' => $referenceWeek->copy()->subWeek()->toDateString(),
            ])),
            'current' => route($routeName, array_merge($baseParams, [
                'week' => now()->toDateString(),
            ])),
            'next' => route($routeName, array_merge($baseParams, [
                'week' => $referenceWeek->copy()->addWeek()->toDateString(),
            ])),
        ];
    }
}
