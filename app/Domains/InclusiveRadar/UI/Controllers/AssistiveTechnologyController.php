<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\UI\Controllers;

use App\Domains\InclusiveRadar\Application\Actions\AssistiveTechnologies\CreateAssistiveTechnologyAction;
use App\Domains\InclusiveRadar\Application\Actions\AssistiveTechnologies\DeleteAssistiveTechnologyAction;
use App\Domains\InclusiveRadar\Application\Actions\AssistiveTechnologies\UpdateAssistiveTechnologyAction;
use App\Domains\InclusiveRadar\Application\Data\AssistiveTechnologies\CreateAssistiveTechnologyData;
use App\Domains\InclusiveRadar\Application\Data\AssistiveTechnologies\ListAssistiveTechnologiesData;
use App\Domains\InclusiveRadar\Application\Data\AssistiveTechnologies\UpdateAssistiveTechnologyData;
use App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies\AssistiveTechnologyFormQuery;
use App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies\AssistiveTechnologyPdfQuery;
use App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies\ListAssistiveTechnologiesQuery;
use App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies\ShowAssistiveTechnologyInspectionQuery;
use App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies\ShowAssistiveTechnologyQuery;
use App\Domains\InclusiveRadar\Domain\Exceptions\AssetCodeAlreadyInUse;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class AssistiveTechnologyController
{
    public function index(ListAssistiveTechnologiesData $filters, ListAssistiveTechnologiesQuery $query, Request $request): InertiaResponse
    {
        $assistiveTechnologies = $query->execute($filters);

        return Inertia::render(
            'InclusiveRadar/AssistiveTechnologies/Index',
            compact('assistiveTechnologies'),
        );
    }

    public function create(AssistiveTechnologyFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/AssistiveTechnologies/Create',
            $form->forCreation(),
        );
    }

    public function clone(AssistiveTechnology $assistiveTechnology, AssistiveTechnologyFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/AssistiveTechnologies/Create',
            $form->forCloning($assistiveTechnology),
        );
    }

    public function store(CreateAssistiveTechnologyData $data, CreateAssistiveTechnologyAction $action, Request $request): RedirectResponse
    {
        try {
            $action->execute(
                data: $data,
                registeredBy: (int) $request->user()?->getAuthIdentifier(),
            );
        } catch (AssetCodeAlreadyInUse $exception) {
            return back()
                ->withInput()
                ->withErrors(['asset_code' => $exception->getMessage()]);
        }

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva criada com sucesso!');
    }

    public function show(AssistiveTechnology $assistiveTechnology, ShowAssistiveTechnologyQuery $query): InertiaResponse
    {
        $technology = $query->execute($assistiveTechnology);

        return Inertia::render(
            'InclusiveRadar/AssistiveTechnologies/Show',
            [
                'assistiveTechnology' => $technology,
                'deficiencies' => $technology->deficiencies,
                'inspections' => $technology->inspections,
            ],
        );
    }

    public function update(UpdateAssistiveTechnologyData $data, AssistiveTechnology $assistiveTechnology, UpdateAssistiveTechnologyAction $action, Request $request): RedirectResponse
    {
        try {
            $action->execute(
                technology: $assistiveTechnology,
                data: $data,
                registeredBy: (int) $request->user()?->getAuthIdentifier(),
            );
        } catch (AssetCodeAlreadyInUse $exception) {
            return back()
                ->withInput()
                ->withErrors(['asset_code' => $exception->getMessage()]);
        }

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva atualizada com sucesso!');
    }

    public function edit(AssistiveTechnology $assistiveTechnology, AssistiveTechnologyFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/AssistiveTechnologies/Edit',
            $form->forUpdate($assistiveTechnology),
        );
    }

    /**
     * @throws Throwable
     */
    public function destroy(AssistiveTechnology $assistiveTechnology, DeleteAssistiveTechnologyAction $action): RedirectResponse
    {
        $action->execute($assistiveTechnology);

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia removida com sucesso!');
    }

    public function generatePdf(AssistiveTechnology $assistiveTechnology, AssistiveTechnologyPdfQuery $query): Response
    {
        $technology = $query->execute($assistiveTechnology);
        $pdf = Pdf::loadView(
            'pages.inclusive-radar.assistive-technologies.pdf',
            ['assistiveTechnology' => $technology],
        )
            ->setPaper('a4', 'portrait');

        PdfPageNumberer::apply($pdf);

        return $pdf->stream("TA_{$technology->name}.pdf");
    }

    public function showInspection(AssistiveTechnology $assistiveTechnology, Inspection $inspection, ShowAssistiveTechnologyInspectionQuery $query): InertiaResponse
    {
        $scopedInspection = $query->execute(
            technology: $assistiveTechnology,
            inspection: $inspection,
        );

        return Inertia::render(
            'InclusiveRadar/AssistiveTechnologies/Inspections/Show',
            [
                'assistiveTechnology' => $assistiveTechnology,
                'inspection' => $scopedInspection,
            ],
        );
    }
}
