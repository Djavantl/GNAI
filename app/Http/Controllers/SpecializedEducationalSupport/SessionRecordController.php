<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Session; 
use App\Models\SpecializedEducationalSupport\SessionRecord;
use App\Services\SpecializedEducationalSupport\SessionRecordService;
use App\Http\Requests\SpecializedEducationalSupport\SessionRecordRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Throwable;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentSessionEvaluation;
use Illuminate\Validation\ValidationException;

class SessionRecordController extends Controller
{
    protected SessionRecordService $service;

    public function __construct(SessionRecordService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $sessionRecords = $this->service->index();

            return view(
                'pages.specialized-educational-support.session-records.index',
                compact('sessionRecords')
            );
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao listar os registros do atendimento AEE.');
        }
    }

    public function myRecords(Request $request)
    {
        try {
            $sessionRecords = $this->service->getMyRecords($request->all());

            $students = Student::with('person')->orderBy('id')->get(['id', 'person_id']);

            if ($request->ajax()) {
                return view(
                    'pages.specialized-educational-support.session-records.partials.my-table',
                    compact('sessionRecords')
                )->render();
            }

            return view(
                'pages.specialized-educational-support.session-records.my-records',
                compact('sessionRecords', 'students')
            );
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao listar seus registros de atendimento.');
        }
    }

    /**
     * Formulário de criação
     * Agora recebe a Session de atendimento para listar os alunos vinculados a ela
     */
    public function create(Session $session)
    {
        try {
            $this->service->ensureCanCreateForSession($session);
            $session->load('students.person');

            return view(
                'pages.specialized-educational-support.session-records.create',
                compact('session')
            );
        } catch (Throwable $e) {
            return redirect()
                ->route('specialized-educational-support.sessions.show', $session)
                ->with('error', $e->getMessage());
        }
    }

    public function store(SessionRecordRequest $request)
    {
        try {
            $sessionRecord = $this->service->create($request->validated());

            return redirect()
                ->route('specialized-educational-support.session-records.show', $sessionRecord)
                ->with('success', 'Registro do atendimento AEE criado com sucesso.');
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao criar o registro do atendimento AEE.');
        }
    }

    public function show(SessionRecord $sessionRecord)
    {
        try {
            $sessionRecord = $this->service->show($sessionRecord);
            $session = $sessionRecord->attendanceSession;

            return view(
                'pages.specialized-educational-support.session-records.show',
                compact('sessionRecord', 'session')
            );
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage() ?: 'Erro ao exibir o registro do atendimento AEE.');
        }
    }

    public function edit(SessionRecord $sessionRecord)
    {
        try {
            $this->service->ensureCanManageRecord($sessionRecord, 'editar este registro de atendimento');
            $sessionRecord->load(['studentEvaluations.student.person', 'attendanceSession.students.person']);
            $session = $sessionRecord->attendanceSession;

            return view(
                'pages.specialized-educational-support.session-records.edit',
                compact('sessionRecord', 'session')
            );
        } catch (Throwable $e) {
            return redirect()
                ->route('specialized-educational-support.session-records.show', $sessionRecord)
                ->with('error', $e->getMessage());
        }
    }

    public function update(SessionRecordRequest $request, SessionRecord $sessionRecord)
    {
        try {
            $this->service->update($sessionRecord, $request->validated());

            return redirect()
                ->route('specialized-educational-support.session-records.show', $sessionRecord)
                ->with('success', 'Registro do atendimento AEE atualizado com sucesso.');
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao atualizar o registro do atendimento AEE.');
        }
    }

    public function destroy(SessionRecord $sessionRecord)
    {
        try {
            $sessionId = $sessionRecord->attendance_session_id;
            $this->service->delete($sessionRecord);

            return redirect()
                ->route('specialized-educational-support.sessions.show', $sessionId)
                ->with('success', 'Registro do atendimento AEE removido com sucesso.');
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao remover o registro do atendimento AEE.');
        }
    }

    public function restore(SessionRecord $sessionRecord)
    {
        try {
            $this->service->restore($sessionRecord);

            return redirect()
                ->route('specialized-educational-support.session-records.index')
                ->with('success', 'Registro restaurado com sucesso.');
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao restaurar o registro do atendimento AEE.');
        }
    }

    public function forceDelete(SessionRecord $sessionRecord)
    {
        try {
            $this->service->forceDelete($sessionRecord);

            return redirect()->back()->with('success', 'Removido permanentemente.');
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao remover permanentemente o registro do atendimento AEE.');
        }
    }

    /**
     * Gerar PDF
     * Ajustado para lidar com múltiplos alunos no mesmo documento
     */
    public function generatePdf(SessionRecord $sessionRecord)
    {
        try {
            $sessionRecord = $this->service->show($sessionRecord);
            $sessionRecord->load('attendanceSession.professional.person', 'studentEvaluations.student.person');

            $session = $sessionRecord->attendanceSession;
            $professional = $session->professional;
            $evaluations = $sessionRecord->studentEvaluations;

            $pdf = Pdf::loadView(
                'pages.specialized-educational-support.session-records.pdf',
                compact('sessionRecord', 'session', 'professional', 'evaluations')
            )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

            $date = $session->session_date->format('d-m-Y');
            return $pdf->stream("Registro_atendimento_AEE_{$date}_ID{$sessionRecord->id}.pdf");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage() ?: 'Erro ao gerar o PDF do Atendimento AEE.');
        }
    }

    public function generateStudentPdf(Student $student, SessionRecord $sessionRecord)
    {
        try {
            $sessionRecord = $this->service->studentPdfData($sessionRecord, $student);

            $session = $sessionRecord->attendanceSession;
            $professional = $session->professional;

            $pdf = Pdf::loadView(
                'pages.specialized-educational-support.session-records.pdf',
                compact('sessionRecord', 'session', 'professional')
            )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

            $date = $session->session_date->format('d-m-Y');
            $studentName = str($student->person->name)->slug('-');

            return $pdf->stream("Registro_atendimento_AEE_{$studentName}_{$date}_ID{$sessionRecord->id}.pdf");
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao gerar o PDF do aluno.');
        }
    }

    public function studentIndex(Student $student, Request $request)
    {
        try {
            $sessionEvaluations = $this->service->studentIndex($student, $request->all(), 10);

            $professionals = \App\Models\SpecializedEducationalSupport\Professional::with('person')
                ->get()
                ->sortBy(fn ($professional) => $professional->person->name ?? '')
                ->values();

            if ($request->ajax()) {
                return view(
                    'pages.specialized-educational-support.students.session-records.partials.table',
                    compact('student', 'sessionEvaluations')
                )->render();
            }

            return view(
                'pages.specialized-educational-support.students.session-records.index',
                compact('student', 'sessionEvaluations', 'professionals')
            );
        } catch (Throwable $e) {
            return throw $e;
        }
    }

    public function studentShow(Student $student, StudentSessionEvaluation $evaluation)
    {
        try {
            $evaluation = $this->service->studentShow($student, $evaluation);

            return view(
                'pages.specialized-educational-support.students.session-records.show',
                compact('student', 'evaluation')
            );
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao exibir o registro do aluno.');
        }
    }

    public function studentEdit(Student $student, StudentSessionEvaluation $evaluation)
    {
        try {
            $evaluation = $this->service->studentShow($student, $evaluation);
            $this->service->ensureCanManageEvaluation($evaluation, 'editar esta avaliação do aluno');

            return view(
                'pages.specialized-educational-support.students.session-records.edit',
                compact('student', 'evaluation')
            );
        } catch (Throwable $e) {
            return redirect()
                ->route('specialized-educational-support.students.session-records.show', [$student, $evaluation])
                ->with('error', $e->getMessage());
        }
    }

    public function studentUpdate(Student $student, StudentSessionEvaluation $evaluation, Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => ['required', 'exists:students,id'],
                'is_present' => ['required', 'boolean'],
                'absence_reason' => ['required_if:is_present,0', 'nullable', 'string'],
                'student_participation' => ['required_if:is_present,1', 'nullable', 'string'],
                'adaptations_made' => ['nullable', 'string'],
                'development_evaluation' => ['required_if:is_present,1', 'nullable', 'string'],
                'progress_indicators' => ['nullable', 'string'],
                'recommendations' => ['nullable', 'string'],
                'next_session_adjustments' => ['nullable', 'string'],
            ], [
                'absence_reason.required_if' => 'A justificativa é obrigatória para aluno ausente.',
                'student_participation.required_if' => 'A participação é obrigatória para aluno presente.',
                'development_evaluation.required_if' => 'A avaliação de desenvolvimento é obrigatória para aluno presente.',
            ]);

            $this->service->studentUpdate($student, $evaluation, $validated);

            return redirect()
                ->route('specialized-educational-support.students.session-records.show', [$student, $evaluation])
                ->with('success', 'Avaliação do aluno atualizada com sucesso.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function studentDestroy(Student $student, StudentSessionEvaluation $evaluation)
    {
        try {
            $this->service->studentDelete($student, $evaluation);

            return redirect()
                ->route('specialized-educational-support.students.session-records.index', $student)
                ->with('success', 'Avaliação do aluno removida com sucesso.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

}
