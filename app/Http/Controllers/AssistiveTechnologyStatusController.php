<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssistiveTechnologyStatusRequest;
use App\Http\Requests\UpdateAssistiveTechnologyStatusRequest;
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

    public function store(StoreAssistiveTechnologyStatusRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('assistive-technology-statuses.index')
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

    public function update(UpdateAssistiveTechnologyStatusRequest $request, AssistiveTechnologyStatus $assistiveTechnologyStatus)
    {
        $this->service->update(
            $assistiveTechnologyStatus,
            $request->validated()
        );

        return redirect()
            ->route('assistive-technology-statuses.index')
            ->with('success', 'Status atualizado com sucesso!');
    }

    public function deactivate(AssistiveTechnologyStatus $assistiveTechnologyStatus)
    {
        $this->service->deactivate($assistiveTechnologyStatus);

        return redirect()
            ->route('assistive-technology-statuses.index')
            ->with('success', 'Status desativado com sucesso!');
    }

    public function destroy(AssistiveTechnologyStatus $assistiveTechnologyStatus)
    {
        $this->service->delete($assistiveTechnologyStatus);

        return redirect()
            ->route('assistive-technology-statuses.index')
            ->with('success', 'Registro removido!');
    }

}
