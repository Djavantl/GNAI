<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\TypeAttributeAssignmentRequest;
use App\Models\InclusiveRadar\ResourceType;
use App\Models\InclusiveRadar\TypeAttributeAssignment;
use App\Services\InclusiveRadar\TypeAttributeAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TypeAttributeAssignmentController extends Controller
{
    protected TypeAttributeAssignmentService $service;

    public function __construct(TypeAttributeAssignmentService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        $assignments = $this->service->listAll();

        return view(
            'pages.inclusive-radar.attribute-assignments.index',
            compact('assignments')
        );
    }

    public function create(): View
    {
        $data = $this->service->getCreateData();

        return view(
            'pages.inclusive-radar.attribute-assignments.create',
            $data
        );
    }

    public function store(TypeAttributeAssignmentRequest $request): RedirectResponse
    {
        $this->service->assignAttributesToType(
            $request->type_id,
            $request->input('attribute_ids', [])
        );

        return redirect()
            ->route('inclusive-radar.type-attribute-assignments.index')
            ->with('success', 'Vínculos processados com sucesso!');
    }

    public function edit(ResourceType $assignment): View
    {
        $data = $this->service->getEditData($assignment);

        return view(
            'pages.inclusive-radar.attribute-assignments.edit',
            $data
        );
    }

    public function update(TypeAttributeAssignmentRequest $request, ResourceType $assignment): RedirectResponse
    {
        $this->service->syncAttributes(
            $assignment->id,
            $request->input('attribute_ids', [])
        );

        return redirect()
            ->route('inclusive-radar.type-attribute-assignments.index')
            ->with('success', 'Atributos atualizados com sucesso para este tipo!');
    }

    public function destroy(ResourceType $assignment): RedirectResponse
    {
        $this->service->removeAssignment($assignment);

        return redirect()
            ->route('inclusive-radar.type-attribute-assignments.index')
            ->with('success', 'Todos os vínculos deste tipo foram removidos!');
    }

    public function getAttributesByType(ResourceType $resourceType)
    {
        $attributes = $this->service->getAttributesByTypeId($resourceType->id);

        return response()->json($attributes);
    }
}
