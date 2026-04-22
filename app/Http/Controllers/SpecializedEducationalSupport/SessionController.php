<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecializedEducationalSupport\SessionRequest;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Session;
use App\Models\SpecializedEducationalSupport\Student;
use App\Services\SpecializedEducationalSupport\SessionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    protected SessionService $service;

    public function __construct(SessionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // 1. Sessões para a Tabela (mantém filtros originais)
        $sessions = $this->service->index($request->all());

        // 2. Dados para os Selects (usados na tabela e na agenda)
        $students = Student::with('person')->orderBy('id')->get();
        $professionals = Professional::with('person')->orderBy('id')->get();

        // 3. Agenda Semanal com filtros específicos
        $agenda = $this->service->getWeeklyAgenda([
            'week'         => $request->input('week', now()->toDateString()),
            'student'      => $request->input('student_agenda'),      // Novo filtro
            'professional' => $request->input('professional_agenda'), // Novo filtro
        ]);

        // 4. Navegação de semanas (preservando filtros da agenda)
        $weekNavigation = $this->buildWeekNavigation(
            $request,
            'specialized-educational-support.sessions.index'
        );

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.sessions.partials.table',
                compact('sessions')
            )->render();
        }

        return view(
            'pages.specialized-educational-support.sessions.index',
            compact('sessions', 'students', 'professionals', 'agenda', 'weekNavigation')
        );
    }

    public function create()
    {
        $students = Student::with('person')
            ->orderBy('id')
            ->get();

        $professionals = Professional::with('person')
            ->orderBy('id')
            ->get();

        $timeOptions = $this->service->getAvailableTimeOptions();

        return view('pages.specialized-educational-support.sessions.create', [
            'students' => $students,
            'professionals' => $professionals,
            'startTimes' => $timeOptions['start'],
            'endTimes' => $timeOptions['end'],
        ]);
    }

    public function store(SessionRequest $request)
    {
        try {
            $this->service->create($request->validated());

            return redirect()
                ->route('specialized-educational-support.sessions.index')
                ->with('success', 'Sessão agendada com sucesso.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocorreu um erro inesperado: ' . $e->getMessage());
        }
    }

    public function show(Session $session)
    {
        $session = $this->service->show($session);

        return view('pages.specialized-educational-support.sessions.show', compact('session'));
    }

    public function edit(Session $session)
    {
        try {
            $this->service->ensureCanEdit($session);
            $timeOptions = $this->service->getAvailableTimeOptions();
            $session->load(['students.person', 'professional.person']);

            return view('pages.specialized-educational-support.sessions.edit', [
                'startTimes' => $timeOptions['start'],
                'endTimes' => $timeOptions['end'],
                'session' => $session,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('specialized-educational-support.sessions.show', $session)
                ->with('error', $e->validator->errors()->first('session'));
        }
    }

    public function update(SessionRequest $request, Session $session)
    {
        try {
            $this->service->update($session, $request->validated());

            return redirect()
                ->route('specialized-educational-support.sessions.show', $session)
                ->with('success', 'Sessão atualizada com sucesso.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    public function indexByStudent(Student $student, Request $request)
    {
        $sessions = Session::query()
            ->with(['professional.person', 'students.person', 'sessionRecord'])
            ->student($student->id)
            ->professional($request->professional ?? null)
            ->type($request->type ?? null)
            ->status($request->status ?? null)
            ->orderByDesc('session_date')
            ->paginate(10)
            ->withQueryString();

        $professionals = Professional::with('person')
            ->orderBy('id')
            ->get(['id', 'person_id']);

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.sessions.partials.table-student',
                compact('sessions', 'student')
            )->render();
        }

        return view(
            'pages.specialized-educational-support.sessions.student-index',
            compact('sessions', 'student', 'professionals')
        );
    }

    public function mySessions(Request $request)
    {
        $sessions = $this->service->getMySessions($request->all());

        $students = Student::with('person')
            ->orderBy('id')
            ->get(['id', 'person_id']);

        $agenda = $this->service->getMyWeeklyAgenda([
            'week' => $request->input('week', now()->toDateString()),
            'student' => $request->input('student_agenda'),
        ]);

        $weekNavigation = $this->buildWeekNavigation(
            $request,
            'specialized-educational-support.sessions.my-sessions'
        );

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.sessions.partials.my-table',
                compact('sessions')
            )->render();
        }

        return view(
            'pages.specialized-educational-support.sessions.my-sessions',
            compact('sessions', 'students', 'agenda', 'weekNavigation')
        );
    }

    public function createForStudent(Student $student)
    {
        $professionals = Professional::with('person')->orderBy('id')->get();
        $timeOptions = $this->service->getAvailableTimeOptions();
        $students = Student::with('person')->get();

        return view('pages.specialized-educational-support.sessions.create-fixed', [
            'student' => $student,
            'students' => $students,
            'professionals' => $professionals,
            'startTimes' => $timeOptions['start'],
            'endTimes' => $timeOptions['end'],
        ]);
    }

    public function cancel(Session $session, Request $request)
    {
        $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:5']
        ], [
            'cancellation_reason.required' => 'O motivo do cancelamento é obrigatório.'
        ]);

        try {
            $this->service->cancel($session, $request->cancellation_reason);
            return redirect()->back()->with('success', 'Sessão cancelada e participantes notificados.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao cancelar: ' . $e->getMessage());
        }
    }

    public function destroy(Session $session)
    {
        $this->service->delete($session);

        return redirect()
            ->route('specialized-educational-support.sessions.index')
            ->with('success', 'Sessão removida com sucesso.');
    }

    public function restore(Session $session)
    {
        $this->service->restore($session);

        return redirect()
            ->route('specialized-educational-support.sessions.index')
            ->with('success', 'Sessão restaurada com sucesso.');
    }

    public function forceDelete(Session $session)
    {
        $this->service->forceDelete($session);

        return redirect()->back()->with('success', 'Removido permanentemente.');
    }

    public function availability(Request $request)
    {
        $studentIds = $request->input('student_ids', []);
        $professionalId = $request->input('professional');
        $date = $request->input('date');

        if (!$date || !$professionalId || empty($studentIds)) {
            return response()->json(['slots' => []]);
        }

        $sessions = Session::whereDate('session_date', $date)
            ->where(function ($q) use ($professionalId, $studentIds) {
                $q->where('professional_id', $professionalId)
                ->orWhereHas('students', function ($sq) use ($studentIds) {
                    $sq->whereIn('students.id', $studentIds);
                });
            })
            ->where(function ($q) {
                $q->whereNull('status')
                ->orWhereRaw('LOWER(status) <> ?', ['cancelada']);
            })
            ->with(['students.person', 'professional.person'])
            ->get();

        $slots = [];
        $periods = [
            ['start' => '08:00', 'end' => '12:00'],
            ['start' => '14:00', 'end' => '17:00']
        ];

        foreach ($periods as $period) {
            $time = Carbon::parse($period['start']);
            $endTime = Carbon::parse($period['end']);

            while ($time < $endTime) {
                $slotStart = $time->copy();
                $slotEnd = $time->copy()->addMinutes(30);

                $conflicts = $sessions->filter(function ($s) use ($slotStart, $slotEnd) {
                    $sessionStart = Carbon::parse($s->start_time);
                    $sessionEnd = Carbon::parse($s->end_time);
                    return $sessionStart < $slotEnd && $sessionEnd > $slotStart;
                });

                $occupants = [];

                if ($conflicts->isNotEmpty()) {
                    foreach ($conflicts as $session) {
                        if ($session->professional_id == $professionalId) {
                            $occupants[] = 'Profissional';
                        }

                        $intersect = $session->students->whereIn('id', $studentIds);

                        foreach ($intersect as $student) {
                            $occupants[] = explode(' ', $student->person->name)[0];
                        }
                    }
                }

                $occupants = array_unique($occupants);

                $slots[] = [
                    'time' => $slotStart->format('H:i'),
                    'busy' => !empty($occupants),
                    'busy_type' => implode(', ', $occupants)
                ];

                $time->addMinutes(30);
            }
        }

        return response()->json(['slots' => $slots]);
    }

    private function buildWeekNavigation(Request $request, string $routeName): array
    {
        $referenceWeek = Carbon::parse($request->input('week', now()->toDateString()))
            ->startOfWeek(Carbon::MONDAY);

        // Inclui student_agenda e professional_agenda nos links de navegação
        $baseParams = collect($request->except(['week', 'page']))
            ->filter(fn ($value) => $value !== null && $value !== '')
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
