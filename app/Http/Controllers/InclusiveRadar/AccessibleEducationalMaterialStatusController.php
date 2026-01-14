<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AccessibleEducationalMaterialStatusRequest;
use App\Models\InclusiveRadar\AccessibleEducationalMaterialStatus;
use App\Services\InclusiveRadar\AccessibleEducationalMaterialStatusService;

class AccessibleEducationalMaterialStatusController extends Controller
{
    protected AccessibleEducationalMaterialStatusService $service;

    public function __construct(
        AccessibleEducationalMaterialStatusService $service
    ) {
        $this->service = $service;
    }

    public function index()
    {
        $statuses = $this->service->listAll();

        return view(
            'inclusive-radar.accessible-educational-material-statuses.index',
            compact('statuses')
        );
    }

    public function create()
    {
        return view('inclusive-radar.accessible-educational-material-statuses.create');
    }

    public function store(
        AccessibleEducationalMaterialStatusRequest $request
    ) {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.accessible-educational-material-statuses.index')
            ->with('success', 'Status criado com sucesso!');
    }

    public function edit(
        AccessibleEducationalMaterialStatus $status
    ) {
        return view(
            'inclusive-radar.accessible-educational-material-statuses.edit',
            ['accessibleEducationalMaterialStatus' => $status]
        );
    }

    public function update(
        AccessibleEducationalMaterialStatusRequest $request,
        AccessibleEducationalMaterialStatus $status
    ) {
        $this->service->update(
            $status,
            $request->validated()
        );

        return redirect()
            ->route('inclusive-radar.accessible-educational-material-statuses.index')
            ->with('success', 'Status atualizado com sucesso!');
    }

    public function toggleActive(
        AccessibleEducationalMaterialStatus $status
    ) {
        $status = $this->service->toggleActive($status);

        $message = $status->is_active
            ? 'Status ativado com sucesso!'
            : 'Status desativado com sucesso!';

        return redirect()
            ->route('inclusive-radar.accessible-educational-material-statuses.index')
            ->with('success', $message);
    }

    public function destroy(
        AccessibleEducationalMaterialStatus $status
    ) {
        $this->service->delete($status);

        return redirect()
            ->route('inclusive-radar.accessible-educational-material-statuses.index')
            ->with('success', 'Registro removido!');
    }

}
