<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\ResourceStatusRequest;
use App\Models\InclusiveRadar\ResourceStatus;
use App\Services\InclusiveRadar\ResourceStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ResourceStatusController extends Controller
{
    protected ResourceStatusService $service;

    public function __construct(ResourceStatusService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        $statuses = $this->service->listAll();

        return view(
            'inclusive-radar.resource-statuses.index',
            compact('statuses')
        );
    }

    public function create(): View
    {
        return view('inclusive-radar.resource-statuses.create');
    }

    public function store(ResourceStatusRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.resource-statuses.index')
            ->with('success', 'Status criado com sucesso!');
    }

    public function edit(ResourceStatus $resourceStatus): View
    {
        return view(
            'inclusive-radar.resource-statuses.edit',
            compact('resourceStatus')
        );
    }

    public function update(ResourceStatusRequest $request, ResourceStatus $resourceStatus): RedirectResponse
    {
        $this->service->update($resourceStatus, $request->validated());

        return redirect()
            ->route('inclusive-radar.resource-statuses.index')
            ->with('success', 'Status atualizado com sucesso!');
    }

    public function toggleActive(ResourceStatus $resourceStatus): RedirectResponse
    {
        $status = $this->service->toggleActive($resourceStatus);

        $message = $status->is_active
            ? 'Status ativado com sucesso!'
            : 'Status desativado com sucesso!';

        return redirect()
            ->route('inclusive-radar.resource-statuses.index')
            ->with('success', $message);
    }

    public function destroy(ResourceStatus $resourceStatus): RedirectResponse
    {
        $this->service->delete($resourceStatus);

        return redirect()
            ->route('inclusive-radar.resource-statuses.index')
            ->with('success', 'Status removido com sucesso!');
    }
}
