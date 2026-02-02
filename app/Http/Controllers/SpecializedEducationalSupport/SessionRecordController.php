<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SpecializedEducationalSupport\Session;
use App\Models\SpecializedEducationalSupport\SessionRecord;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Services\SpecializedEducationalSupport\SessionRecordService;
use App\Http\Requests\SpecializedEducationalSupport\SessionRecordRequest;

class SessionRecordController extends Controller
{
    protected SessionRecordService $service;

    public function __construct(SessionRecordService $service)
    {
        $this->service = $service;
    }

    // listar todos os registros
    public function index()
    {
        $sessionRecords = $this->service->index();

        return view(
            'specialized-educational-support.session-records.index',
            compact('sessionRecords')
        );
    }

    // formulário de criação
    public function create(Session $session)
    {
        return view(
            'specialized-educational-support.session-records.create',
            compact('session')
        );
    }

    // salvar registro
    public function store(SessionRecordRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('specialized-educational-support.sessions.show', $request->attendance_sessions_id)
            ->with('success', 'Registro da sessão criado com sucesso.');
    }

    // mostrar um registro
    public function show(SessionRecord $sessionRecord)
    {
        $sessionRecord = $this->service->show($sessionRecord);

        return view(
            'specialized-educational-support.session-records.show',
            compact('sessionRecord')
        );
    }

    // formulário de edição
    public function edit(SessionRecord $sessionRecord)
    {
        return view(
            'specialized-educational-support.session-records.edit',
            compact('sessionRecord')
        );
    }

    // atualizar registro
    public function update(SessionRecordRequest $request, SessionRecord $sessionRecord)
    {
        $this->service->update($sessionRecord, $request->validated());

        return redirect()
            ->route(
                'specialized-educational-support.session-records.show',
                $sessionRecord
            )
            ->with('success', 'Registro da sessão atualizado com sucesso.');
    }

    // soft delete
    public function destroy(SessionRecord $sessionRecord)
    {
        $this->service->delete($sessionRecord);

        return redirect()
            ->route('specialized-educational-support.sessions.show', $sessionRecord->attendance_sessions_id)
            ->with('success', 'Registro da sessão removido com sucesso.');
    }

    // restaurar (soft delete)
    public function restore(SessionRecord $sessionRecord)
    {
        $this->service->restore($sessionRecord);

        return redirect()
            ->route('specialized-educational-support.sessions.index')
            ->with('success', 'restaurada com sucesso.');
    }

    //excluir definitivamente

    public function forceDelete(SessionRecord $sessionRecord)
    {
        $this->service->forceDelete($sessionRecord);

        return redirect()->back()->with('success', 'Removido permanentemente.');
    }
}
