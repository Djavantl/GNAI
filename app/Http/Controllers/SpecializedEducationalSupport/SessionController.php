<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Session;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Services\SpecializedEducationalSupport\SessionService;
use App\Http\Requests\SpecializedEducationalSupport\SessionRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SessionController extends Controller
{
    protected SessionService $service;

    public function __construct(SessionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $sessions = Session::with([
            'students.person',
            'professional.person'
        ])->get(); 

        return view(
            'pages.specialized-educational-support.sessions.index',
            compact('sessions')
        );
    }

    public function create()
    {
        $students = Student::all();
        $professionals = Professional::all();
        
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
            // Deixa o Laravel tratar o erro de validação (volta com os erros)
            throw $e; 
        } catch (\Exception $e) {
            // Para qualquer outro erro (banco de dados, email, etc) volta com uma mensagem geral
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
        $timeOptions = $this->service->getAvailableTimeOptions();
        $session->load('students.person');

        return view('pages.specialized-educational-support.sessions.edit', [
            'startTimes' => $timeOptions['start'],
            'endTimes'   => $timeOptions['end'],
            'session'    => $session, 
        ]);
    }

    // 1. Index filtrada por Aluno
    public function indexByStudent(Student $student)
    {
        $sessions = $this->service->getSessionsByStudent($student->id);
        return view('pages.specialized-educational-support.sessions.student-index', compact('sessions', 'student'));
    }

    // 2. Create com Aluno Fixo
    public function createForStudent(Student $student)
    {
        $professionals = Professional::all();
        $timeOptions = $this->service->getAvailableTimeOptions();
        $students = Student::with('person')->get();

        return view('pages.specialized-educational-support.sessions.create-fixed', [
            'student'      => $student,
            'students'      => $students,
            'professionals'=> $professionals,
            'startTimes'   => $timeOptions['start'],
            'endTimes'     => $timeOptions['end'],
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


    // 3. Index do Profissional Logado
    public function mySessions()
    {
        $professionalId = auth()->user()->professional->id; 
        $sessions = $this->service->getSessionsByProfessional($professionalId);
        
        return view('pages.specialized-educational-support.sessions.index', compact('sessions'));
    }

    public function update(Session $session, SessionRequest $request)
    {
        $this->service->update($session, $request->validated());

        return redirect()
            ->route('specialized-educational-support.sessions.show', $session)
            ->with('success', 'Sessão atualizada com sucesso.');
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
            ->with(['students.person', 'professional.person']) // Carregar nomes
            ->get();

        $slots = [];
        $periods = [['start' => '08:00', 'end' => '12:00'], ['start' => '14:00', 'end' => '17:00']];

        foreach ($periods as $period) { 
            $time = Carbon::parse($period['start']);
            $endTime = Carbon::parse($period['end']);

            while ($time < $endTime) {
                $slotStart = $time->copy();
                $slotEnd = $time->copy()->addMinutes(30);

                // Filtrar todas as sessões que batem com este horário (pode ser mais de uma)
                $conflicts = $sessions->filter(function ($s) use ($slotStart, $slotEnd) {
                    $sessionStart = Carbon::parse($s->start_time);
                    $sessionEnd = Carbon::parse($s->end_time);
                    return $sessionStart < $slotEnd && $sessionEnd > $slotStart;
                });

                $occupants = [];
                if ($conflicts->isNotEmpty()) {
                    foreach ($conflicts as $session) {
                        // Se o profissional da sessão for o que estamos buscando
                        if ($session->professional_id == $professionalId) {
                            $occupants[] = 'Profissional';
                        }
                        
                        // Alunos desta sessão que estão no nosso array de busca
                        $intersect = $session->students->whereIn('id', $studentIds);
                        foreach ($intersect as $student) {
                            $occupants[] = explode(' ', $student->person->name)[0]; // Apenas primeiro nome
                        }
                    }
                }

                $occupants = array_unique($occupants);

                $slots[] = [
                    'time' => $slotStart->format('H:i'),
                    'busy' => !empty($occupants),
                    'busy_type' => implode(', ', $occupants) // Ex: "Profissional, João, Maria"
                ];
                $time->addMinutes(30);
            }
        }
        return response()->json(['slots' => $slots]);
    }
}
