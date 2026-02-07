<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AssistiveTechnologyRequest;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\ResourceType;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\InclusiveRadar\AssistiveTechnologyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssistiveTechnologyController extends Controller
{
    public function __construct(
        protected AssistiveTechnologyService $service
    ) {}

    public function index(): View
    {
        return view('pages.inclusive-radar.assistive-technologies.index', [
            'assistiveTechnologies' => $this->service->index()
        ]);
    }

    public function create(): View
    {
        $deficiencies = Deficiency::orderBy('name')->get();
        $resourceTypes = ResourceType::active()->forAssistiveTechnology()->orderBy('name')->get();

        return view('pages.inclusive-radar.assistive-technologies.create', compact('deficiencies', 'resourceTypes'));
    }

    public function store(AssistiveTechnologyRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva criada com sucesso!');
    }

    public function show(AssistiveTechnology $assistiveTechnology): View
    {
        $assistiveTechnology->load([
            'type',
            'resourceStatus',
            'deficiencies',
            'inspections.images',
            'loans',
            'attributeValues.attribute'
        ]);

        $attributeValues = $assistiveTechnology->attributeValues
            ->pluck('value', 'attribute_id')
            ->toArray();

        $resourceTypes = ResourceType::active()
            ->forAssistiveTechnology()
            ->with('attributes')
            ->orderBy('name')
            ->get();

        return view('pages.inclusive-radar.assistive-technologies.show', compact(
            'assistiveTechnology',
            'attributeValues',
            'resourceTypes'
        ));
    }

    public function edit(AssistiveTechnology $assistiveTechnology): View
    {
        $assistiveTechnology->load([
            'deficiencies',
            'inspections.images',
            'attributeValues.attribute'
        ]);

        $attributeValues = $assistiveTechnology->attributeValues
            ->pluck('value', 'attribute_id')
            ->toArray();

        $resourceTypes = ResourceType::active()
            ->forAssistiveTechnology()
            ->orderBy('name')
            ->get();

        $deficiencies = Deficiency::orderBy('name')->get();

        return view('pages.inclusive-radar.assistive-technologies.edit', compact(
            'assistiveTechnology',
            'attributeValues',
            'resourceTypes',
            'deficiencies'
        ));
    }

    public function update(AssistiveTechnologyRequest $request, AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->update($assistiveTechnology, $request->validated());

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva atualizada com sucesso!');
    }

    public function toggleActive(AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->toggleActive($assistiveTechnology);

        return redirect()->back()->with('success', 'Status atualizado com sucesso!');
    }

    public function destroy(AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->delete($assistiveTechnology);

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia removida com sucesso!');
    }
}
