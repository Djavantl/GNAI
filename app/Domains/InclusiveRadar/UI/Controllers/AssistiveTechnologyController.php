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
use App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies\ShowAssistiveTechnologyQuery;
use App\Domains\InclusiveRadar\Application\Queries\Inspections\ShowAssistiveTechnologyInspectionQuery;
use App\Domains\InclusiveRadar\Domain\Exceptions\AssetCodeAlreadyInUse;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

final class AssistiveTechnologyController extends Controller
{
    public function index(
        ListAssistiveTechnologiesData $filters,
        ListAssistiveTechnologiesQuery $query,
        Request $request,
    ): View {
        $assistiveTechnologies = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.assistive-technologies.partials.table',
                compact('assistiveTechnologies'),
            );
        }

        return view(
            'pages.inclusive-radar.assistive-technologies.index',
            compact('assistiveTechnologies'),
        );
    }

    public function create(AssistiveTechnologyFormQuery $form): View
    {
        return view(
            'pages.inclusive-radar.assistive-technologies.create',
            $form->forCreation(),
        );
    }

    public function store(CreateAssistiveTechnologyData $data, CreateAssistiveTechnologyAction $action): RedirectResponse
    {
        try {
            $action->execute(
                data: $data,
                registeredBy: (int) auth()->id(),
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

    public function show(
        AssistiveTechnology $assistiveTechnology,
        ShowAssistiveTechnologyQuery $query,
    ): View {
        $technology = $query->execute($assistiveTechnology);

        return view(
            'pages.inclusive-radar.assistive-technologies.show',
            [
                'assistiveTechnology' => $technology,
                'deficiencies' => $technology->deficiencies,
                'inspections' => $technology->inspections,
            ],
        );
    }

    public function update(
        UpdateAssistiveTechnologyData $data,
        AssistiveTechnology $assistiveTechnology,
        UpdateAssistiveTechnologyAction $action,
    ): RedirectResponse {
        try {
            $action->execute(
                technology: $assistiveTechnology,
                data: $data,
                registeredBy: (int) auth()->id(),
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

    public function edit(
        AssistiveTechnology $assistiveTechnology,
        AssistiveTechnologyFormQuery $form,
    ): View {
        return view(
            'pages.inclusive-radar.assistive-technologies.edit',
            $form->forUpdate($assistiveTechnology),
        );
    }

    public function destroy(
        AssistiveTechnology $assistiveTechnology,
        DeleteAssistiveTechnologyAction $action,
    ): RedirectResponse {
        $action->execute($assistiveTechnology);

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia removida com sucesso!');
    }

    public function generatePdf(
        AssistiveTechnology $assistiveTechnology,
        AssistiveTechnologyPdfQuery $query,
    ): Response {
        $technology = $query->execute($assistiveTechnology);
        $pdf = Pdf::loadView(
            'pages.inclusive-radar.assistive-technologies.pdf',
            ['assistiveTechnology' => $technology],
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

        return $pdf->stream("TA_{$technology->name}.pdf");
    }

    public function showInspection(
        AssistiveTechnology $assistiveTechnology,
        Inspection $inspection,
        ShowAssistiveTechnologyInspectionQuery $query,
    ): View {
        $scopedInspection = $query->execute(
            technology: $assistiveTechnology,
            inspection: $inspection,
        );

        return view(
            'pages.inclusive-radar.assistive-technologies.inspections.show',
            [
                'assistiveTechnology' => $assistiveTechnology,
                'inspection' => $scopedInspection,
            ],
        );
    }
}
