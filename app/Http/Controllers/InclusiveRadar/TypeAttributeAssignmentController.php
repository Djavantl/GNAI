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
        $assignments = TypeAttributeAssignment::with(['type', 'attribute'])->get();
        return view('inclusive-radar.attribute-assignments.index', compact('assignments'));
    }

    public function create(): View
    {
        $types = ResourceType::where('is_active', true)->get();
        $attributes = TypeAttribute::where('is_active', true)->get();

        return view('inclusive-radar.attribute-assignments.create', compact('types', 'attributes'));
    }

    public function store(TypeAttributeAssignmentRequest $request): RedirectResponse
    {
        $this->service->assignAttributesToType(
            $request->type_id,
            $request->input('attribute_ids', [])
        );

        return redirect()
            ->route('inclusive-radar.type-attribute-assignments.index')
            ->with('success', 'Vínculos processados com sucesso.');
    }

    public function edit($type_id): View
    {
        $type = ResourceType::findOrFail($type_id);
        $types = ResourceType::where('is_active', true)->get();
        $attributes = TypeAttribute::where('is_active', true)->get();

        $assignedAttributeIds = TypeAttributeAssignment::where('type_id', $type_id)
            ->pluck('attribute_id')
            ->toArray();

        return view('inclusive-radar.attribute-assignments.edit', compact('type', 'types', 'attributes', 'assignedAttributeIds'));
    }

    public function update(TypeAttributeAssignmentRequest $request, $id): RedirectResponse
    {
        $this->service->syncAttributes($request->type_id, $request->input('attribute_ids', []));

        return redirect()
            ->route('inclusive-radar.type-attribute-assignments.index')
            ->with('success', 'Atributos atualizados com sucesso para este tipo!');
    }

    public function destroy(TypeAttributeAssignment $assignment)
    {
        $this->service->removeAssignment($assignment->id);
        return redirect()->route('inclusive-radar.type-attribute-assignments.index')->with('success', 'Vinculação removida com sucesso!');
    }

    public function getAttributesByType($typeId)
    {
        $attributes = $this->service->getAttributesByTypeId($typeId);
        return response()->json($attributes);
    }
}
