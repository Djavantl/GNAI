<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AssistiveTechnologyRequest;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Services\InclusiveRadar\AssistiveTechnologyService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssistiveTechnologyController extends Controller
{
    public function __construct(
        protected AssistiveTechnologyService $service
    ) {}

    public function index(): View
    {
        $assistiveTechnologies = AssistiveTechnology::with([
            'type',
            'resourceStatus',
            'deficiencies'
        ])->orderBy('name')->get();

        return view(
            'pages.inclusive-radar.assistive-technologies.index',
            compact('assistiveTechnologies')
        );
    }

    public function create(): View
    {
        // A view create recebe $deficiencies e $resourceTypes via View Composer
        return view('pages.inclusive-radar.assistive-technologies.create');
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

        return view(
            'pages.inclusive-radar.assistive-technologies.show',
            compact('assistiveTechnology', 'attributeValues')
        );
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

        // A view edit recebe $deficiencies e $resourceTypes via View Composer
        return view(
            'pages.inclusive-radar.assistive-technologies.edit',
            compact('assistiveTechnology', 'attributeValues')
        );
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

    public function generatePdf(AssistiveTechnology $assistiveTechnology)
    {
        $assistiveTechnology->load([
            'type',
            'resourceStatus',
            'deficiencies',
            'attributeValues.attribute',
            'inspections.images'
        ]);

        $attributeValues = $assistiveTechnology->attributeValues
            ->pluck('value', 'attribute_id')
            ->toArray();

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.assistive-technologies.pdf',
            compact('assistiveTechnology', 'attributeValues')
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

        return $pdf->stream("TA_{$assistiveTechnology->name}.pdf");
    }
}
