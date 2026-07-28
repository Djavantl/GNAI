<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use App\Http\Controllers\Controller;
use App\Http\Requests\SpecializedEducationalSupport\PedagogicalRecordRequest;
use App\Models\SpecializedEducationalSupport\PedagogicalRecord;
use App\Models\SpecializedEducationalSupport\Student;
use App\Services\SpecializedEducationalSupport\PedagogicalRecordService;
use App\Support\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Throwable;

class PedagogicalRecordController extends Controller
{
    public function __construct(private readonly PedagogicalRecordService $service) {}

    public function myRecords(Request $request)
    {
        try {
            $pedagogicalRecords = $this->service->getMyRecords($request->all());
            $students = Student::with('person')->orderBy('id')->get(['id', 'person_id']);

            if ($request->ajax()) {
                return view(
                    'pages.specialized-educational-support.pedagogical-records.partials.my-table',
                    compact('pedagogicalRecords')
                )->render();
            }

            return view(
                'pages.specialized-educational-support.pedagogical-records.my-records',
                compact('pedagogicalRecords', 'students')
            );
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao listar seus atendimentos pedagógicos.');
        }
    }

    public function create(Session $session)
    {
        try {
            $this->service->ensureCanCreateForSession($session);
            $session->load(['students.person', 'professional.person']);

            return view('pages.specialized-educational-support.pedagogical-records.create', compact('session'));
        } catch (Throwable $e) {
            return redirect()
                ->route('specialized-educational-support.sessions.show', $session)
                ->with('error', $e->getMessage());
        }
    }

    public function store(PedagogicalRecordRequest $request)
    {
        try {
            $record = $this->service->create($request->validated());

            return redirect()
                ->route('specialized-educational-support.pedagogical-records.show', $record)
                ->with('success', 'Atendimento pedagógico criado com sucesso.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Erro ao criar o atendimento pedagógico: '.$e->getMessage());
        }
    }

    public function show(PedagogicalRecord $pedagogicalRecord)
    {
        try {
            $pedagogicalRecord = $this->service->show($pedagogicalRecord);

            return view('pages.specialized-educational-support.pedagogical-records.show', compact('pedagogicalRecord'));
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage() ?: 'Erro ao exibir o atendimento pedagógico.');
        }
    }

    public function edit(PedagogicalRecord $pedagogicalRecord)
    {
        try {
            $this->service->ensureCanManageRecord($pedagogicalRecord, 'editar este atendimento pedagógico');
            $pedagogicalRecord->load(['attendanceSession.students.person', 'attendanceSession.professional.person']);

            return view('pages.specialized-educational-support.pedagogical-records.edit', compact('pedagogicalRecord'));
        } catch (Throwable $e) {
            return redirect()
                ->route('specialized-educational-support.pedagogical-records.show', $pedagogicalRecord)
                ->with('error', $e->getMessage());
        }
    }

    public function update(PedagogicalRecordRequest $request, PedagogicalRecord $pedagogicalRecord)
    {
        try {
            $pedagogicalRecord = $this->service->update($pedagogicalRecord, $request->validated());

            return redirect()
                ->route('specialized-educational-support.pedagogical-records.show', $pedagogicalRecord)
                ->with('success', 'Atendimento pedagógico atualizado com sucesso.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar o atendimento pedagógico: '.$e->getMessage());
        }
    }

    public function destroy(PedagogicalRecord $pedagogicalRecord)
    {
        try {
            $sessionId = $pedagogicalRecord->attendance_session_id;
            $this->service->delete($pedagogicalRecord);

            return redirect()
                ->route('specialized-educational-support.sessions.show', $sessionId)
                ->with('success', 'Atendimento pedagógico removido com sucesso.');
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao remover o atendimento pedagógico: '.$e->getMessage());
        }
    }

    public function pdf(PedagogicalRecord $pedagogicalRecord)
    {
        try {
            $pedagogicalRecord = $this->service->show($pedagogicalRecord);
            $session = $pedagogicalRecord->attendanceSession;
            $student = $session->students->first();
            $professional = $session->professional;

            $pdf = Pdf::loadView(
                'pages.specialized-educational-support.pedagogical-records.pdf',
                compact('pedagogicalRecord', 'session', 'student', 'professional')
            )->setPaper('a4', 'portrait');

            $date = $session->session_date->format('d-m-Y');
            $studentName = str($student?->person?->name ?? 'aluno')->slug('-');
            PdfPageNumberer::apply($pdf);

            return $pdf->stream("registro-pedagogico-{$studentName}-{$date}.pdf");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage() ?: 'Erro ao gerar o PDF do atendimento pedagógico.');
        }
    }
}
