<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\TypeAttributeAssignmentRequest;
use App\Models\InclusiveRadar\ResourceType;
use App\Models\InclusiveRadar\TypeAttributeAssignment;
use App\Models\InclusiveRadar\TypeAttribute;
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
        $assignments = TypeAttributeAssignment::with(['type', 'attribute'])
            ->get()
            ->sortBy(fn($a) => $a->type->name);

        return view(
            'pages.inclusive-radar.attribute-assignments.index',
            compact('assignments')
        );
    }

    public function create(): View
    {
        $types = ResourceType::where('is_active', true)->whereDoesntHave('attributes')->orderBy('name')->get();
        $attributes = TypeAttribute::where('is_active', true)->orderBy('label')->get();

        return view(
            'pages.inclusive-radar.attribute-assignments.create',
            compact('types', 'attributes')
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

    public function show(ResourceType $assignment): View
    {
        $assignment->load('attributes');

        return view(
            'pages.inclusive-radar.attribute-assignments.show',
            compact('assignment')
        );
    }

    public function edit(ResourceType $assignment): View
    {
        $types = ResourceType::where('is_active', true)->orderBy('name')->get();
        $attributes = TypeAttribute::where('is_active', true)->orderBy('label')->get();
        $assignedAttributeIds = TypeAttributeAssignment::where('type_id', $assignment->id)
            ->pluck('attribute_id')
            ->toArray();

        return view(
            'pages.inclusive-radar.attribute-assignments.edit',
            compact('assignment', 'types', 'attributes', 'assignedAttributeIds')
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
        $this->service->removeAssignment($assignment->id);

        return redirect()
            ->route('inclusive-radar.type-attribute-assignments.index')
            ->with('success', 'Todos os vínculos deste tipo foram removidos!');
    }

    public function getAttributesByType(ResourceType $resourceType)
    {
        $attributes = $resourceType->attributes()
            ->where('is_active', true)
            ->get([
                'type_attributes.id',
                'type_attributes.label',
                'type_attributes.field_type',
                'type_attributes.is_required'
            ]);

        return response()->json($attributes);
    }

}
