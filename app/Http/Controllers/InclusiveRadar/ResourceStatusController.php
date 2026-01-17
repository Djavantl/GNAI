<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\ResourceStatusRequest;
use App\Models\InclusiveRadar\ResourceStatus;
use App\Services\InclusiveRadar\ResourceStatusService;

class ResourceStatusController extends Controller
{
    public function __construct(
        protected ResourceStatusService $service
    ) {}

    public function index()
    {
        $statuses = $this->service->listAll();

        return view(
            'inclusive-radar.resource-statuses.index',
            compact('statuses')
        );
    }

    public function edit(ResourceStatus $resourceStatus)
    {
        return view(
            'inclusive-radar.resource-statuses.edit',
            compact('resourceStatus')
        );
    }

    public function update(
        ResourceStatusRequest $request,
        ResourceStatus $resourceStatus
    ) {
        $this->service->update(
            $resourceStatus,
            $request->validated()
        );

        return redirect()
            ->route('inclusive-radar.resource-statuses.index')
            ->with('success', 'Status atualizado com sucesso!');
    }

    public function toggleActive(ResourceStatus $resourceStatus)
    {
        $status = $this->service->toggleActive($resourceStatus);

        $message = $status->is_active
            ? 'Status ativado com sucesso!'
            : 'Status desativado com sucesso!';

        return redirect()
            ->route('inclusive-radar.resource-statuses.index')
            ->with('success', $message);
    }
}
