<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\TypeAttributeAssignmentRequest;
use App\Models\InclusiveRadar\ResourceType;
use App\Models\InclusiveRadar\TypeAttribute;
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

    public function edit(ResourceType $resourceType): View
    {
        $data = $this->service->getEditData($resourceType);

        return view(
            'pages.inclusive-radar.attribute-assignments.edit',
            $data
        );
    }

    public function update(TypeAttributeAssignmentRequest $request, ResourceType $resourceType): RedirectResponse
    {
        $this->service->syncAttributes(
            $resourceType->id,
            $request->input('attribute_ids', [])
        );

        return redirect()
            ->route('inclusive-radar.type-attribute-assignments.index')
            ->with('success', 'Atributos atualizados com sucesso para este tipo!');
    }

    public function destroy(TypeAttributeAssignment $assignment): RedirectResponse
    {
        $this->service->removeAssignment($assignment->id);

        return redirect()
            ->route('inclusive-radar.type-attribute-assignments.index')
            ->with('success', 'Vinculação removida com sucesso!');
    }

    public function getAttributesByType(ResourceType $resourceType)
    {
        $attributes = $this->service->getAttributesByTypeId($resourceType->id);

        return response()->json($attributes);
    }
}
