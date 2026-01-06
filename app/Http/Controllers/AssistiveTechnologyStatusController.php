<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssistiveTechnologyStatusRequest;
use App\Models\AssistiveTechnologyStatus;
use App\Services\AssistiveTechnologyStatusService;

class AssistiveTechnologyStatusController extends Controller
{

    protected AssistiveTechnologyStatusService $service;

    public function __construct(AssistiveTechnologyStatusService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $statuses = $this->service->listAll();

        return view('assistive-technology-statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('assistive-technology-statuses.create');
    }

    public function store(AssistiveTechnologyStatusRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('assistive-technologies-statuses.index')
            ->with('success', 'Status criado com sucesso!');
    }

    public function show(AssistiveTechnologyStatus $assistiveTechnologyStatus)
    {
        //
    }

    public function edit(AssistiveTechnologyStatus $assistiveTechnologyStatus)
    {
        return view(
            'assistive-technology-statuses.edit',
            compact('assistiveTechnologyStatus')
        );
    }

    public function update(AssistiveTechnologyStatusRequest $request, AssistiveTechnologyStatus $assistiveTechnologyStatus)
    {
        $this->service->update($assistiveTechnologyStatus, $request->validated());

        return redirect()
            ->route('assistive-technologies-statuses.index')
            ->with('success', 'Status atualizado com sucesso!');
    }

    public function toggleActive(AssistiveTechnologyStatus $assistiveTechnologyStatus)
    {
        $status = $this->service->toggleActive($assistiveTechnologyStatus);

        $message = $status->is_active
            ? 'Status ativado com sucesso!'
            : 'Status desativado com sucesso!';

        return redirect()
            ->route('assistive-technologies-statuses.index')
            ->with('success', $message);
    }

    public function destroy(AssistiveTechnologyStatus $assistiveTechnologyStatus)
    {
        $this->service->delete($assistiveTechnologyStatus);

        return redirect()
            ->route('assistive-technologies-statuses.index')
            ->with('success', 'Registro removido!');
    }

}
