<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AssistiveTechnologyRequest;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\InclusiveRadar\AssistiveTechnologyService;
use App\Services\InclusiveRadar\ResourceAttributeValueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssistiveTechnologyController extends Controller
{
    protected AssistiveTechnologyService $service;
    protected ResourceAttributeValueService $attributeService;

    public function __construct(
        AssistiveTechnologyService $service,
        ResourceAttributeValueService $attributeService
    ) {
        $this->service = $service;
        $this->attributeService = $attributeService;
    }

    public function index(): View
    {
        $technologies = $this->service->listAll();
        return view('inclusive-radar.assistive-technologies.index', compact('technologies'));
    }

    public function create(): View
    {
        $deficiencies = Deficiency::where('is_active', true)->get();
        return view('inclusive-radar.assistive-technologies.create', compact('deficiencies'));
    }

    public function store(AssistiveTechnologyRequest $request): RedirectResponse
    {

        $resource = $this->service->store($request->validated());
        $dynamicAttributes = $request->input('attributes', []);

        $this->attributeService->saveValues(
            'assistive_technology',
            $resource->id,
            $dynamicAttributes
        );

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva criada com sucesso!');
    }

    public function edit(AssistiveTechnology $assistiveTechnology): View
    {

        $assistiveTechnology->load(['deficiencies', 'images']);

        $deficiencies = Deficiency::where('is_active', true)->get();

        return view('inclusive-radar.assistive-technologies.edit', compact('assistiveTechnology', 'deficiencies'));
    }

    public function update(AssistiveTechnologyRequest $request, AssistiveTechnology $assistiveTechnology): RedirectResponse
    {

        $this->service->update($assistiveTechnology, $request->validated());
        $dynamicAttributes = $request->input('attributes', []);

        $this->attributeService->saveValues(
            'assistive_technology',
            $assistiveTechnology->id,
            $dynamicAttributes
        );

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva atualizada com sucesso!');
    }

    public function toggleActive(AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $tech = $this->service->toggleActive($assistiveTechnology);

        $message = $tech->is_active
            ? 'Tecnologia assistiva ativada com sucesso!'
            : 'Tecnologia assistiva desativada com sucesso!';

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', $message);
    }

    public function destroy(AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->delete($assistiveTechnology);

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva removida com sucesso!');
    }
}
