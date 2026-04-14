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
            return back()->with('error', 'Erro ao listar os registros da sessão.');
        }
    }

    /**
     * Formulário de criação
     * Agora recebe a Session de atendimento para listar os alunos vinculados a ela
     */
    public function create(Session $session)
    {
        // Carrega os alunos da sessão para que o formulário possa gerar os campos de avaliação
        $session->load('students.person');

        return view(
            'pages.specialized-educational-support.session-records.create',
            compact('session')
        );
    }

    public function store(SessionRecordRequest $request)
    {
        try {
            $sessionRecord = $this->service->create($request->validated());

            return redirect()
                ->route('specialized-educational-support.session-records.show', $sessionRecord)
                ->with('success', 'Registro da sessão criado com sucesso.');
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao criar o registro da sessão.');
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
            return back()->with('error', 'Erro ao exibir o registro da sessão.');
        }
    }

    public function edit(SessionRecord $sessionRecord)
    {
        // Carrega o registro com as avaliações e os alunos da sessão original
        $sessionRecord->load(['studentEvaluations.student.person', 'attendanceSession.students.person']);
        $session = $sessionRecord->attendanceSession;

        return view(
            'pages.specialized-educational-support.session-records.edit',
            compact('sessionRecord', 'session')
        );
    }

    public function update(SessionRecordRequest $request, SessionRecord $sessionRecord)
    {
        try {
            $this->service->update($sessionRecord, $request->validated());

            return redirect()
                ->route('specialized-educational-support.session-records.show', $sessionRecord)
                ->with('success', 'Registro da sessão atualizado com sucesso.');
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao atualizar o registro da sessão.');
        }
    }

    public function destroy(SessionRecord $sessionRecord)
    {
        try {
            $sessionId = $sessionRecord->attendance_session_id;
            $this->service->delete($sessionRecord);

            return redirect()
                ->route('specialized-educational-support.sessions.show', $sessionId)
                ->with('success', 'Registro da sessão removido com sucesso.');
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao remover o registro da sessão.');
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
            return back()->with('error', 'Erro ao restaurar o registro da sessão.');
        }
    }

    public function forceDelete(SessionRecord $sessionRecord)
    {
        try {
            $this->service->forceDelete($sessionRecord);

            return redirect()->back()->with('success', 'Removido permanentemente.');
        } catch (Throwable $e) {
            return back()->with('error', 'Erro ao remover permanentemente o registro da sessão.');
        }
    }

    /**
     * Gerar PDF
     * Ajustado para lidar com múltiplos alunos no mesmo documento
     */
    public function generatePdf(SessionRecord $sessionRecord)
    {
        $sessionRecord->load([
            'attendanceSession.professional.person',
            'studentEvaluations.student.person'
        ]);

        $session = $sessionRecord->attendanceSession;
        $professional = $session->professional;
        $evaluations = $sessionRecord->studentEvaluations;

        $pdf = Pdf::loadView(
            'pages.specialized-educational-support.session-records.pdf',
            compact('sessionRecord', 'session', 'professional', 'evaluations')
        )
        ->setPaper('a4', 'portrait')
        ->setOption(['enable_php' => true]);

        // Nome do arquivo usa a data e ID do registro já que pode ter vários alunos
        $date = $session->session_date->format('d-m-Y');
        return $pdf->stream("Registro_Sessao_Geral_{$date}_ID{$sessionRecord->id}.pdf");
    }
}