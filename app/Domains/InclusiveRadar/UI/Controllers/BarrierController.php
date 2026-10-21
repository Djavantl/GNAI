<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\UI\Controllers;

use App\Domains\InclusiveRadar\Application\Actions\Barriers\CreateBarrierAction;
use App\Domains\InclusiveRadar\Application\Actions\Barriers\DeleteBarrierAction;
use App\Domains\InclusiveRadar\Application\Actions\Barriers\UpdateBarrierAction;
use App\Domains\InclusiveRadar\Application\Data\Barriers\CreateBarrierData;
use App\Domains\InclusiveRadar\Application\Data\Barriers\ListBarriersData;
use App\Domains\InclusiveRadar\Application\Data\Barriers\UpdateBarrierData;
use App\Domains\InclusiveRadar\Application\Queries\Barriers\BarrierFormQuery;
use App\Domains\InclusiveRadar\Application\Queries\Barriers\BarrierPdfQuery;
use App\Domains\InclusiveRadar\Application\Queries\Barriers\ListBarriersQuery;
use App\Domains\InclusiveRadar\Application\Queries\Barriers\ShowBarrierInspectionQuery;
use App\Domains\InclusiveRadar\Application\Queries\Barriers\ShowBarrierQuery;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class BarrierController
{
    public function index(ListBarriersData $filters, ListBarriersQuery $query, Request $request): InertiaResponse
    {
        $barriers = $query->execute($filters);

        return Inertia::render('InclusiveRadar/Barriers/Index', [
            'barriers' => $barriers,
            'filters' => [
                'name' => $filters->name ?? '',
                'category' => $filters->category ?? '',
                'priority' => $filters->priority ?? '',
                'status' => $filters->status ?? '',
            ],
            'can' => [
                'create' => $request->user()?->can('barrier.create') ?? false,
                'show' => $request->user()?->can('barrier.show') ?? false,
                'destroy' => $request->user()?->can('barrier.destroy') ?? false,
            ],
        ]);
    }

    public function create(Request $request, BarrierFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/Barriers/Create',
            $form->forCreation(
                selectedInstitutionId: $request->old('institution_id')
                    ? (int) $request->old('institution_id')
                    : null,
            ) + ['canSubmit' => $request->user()?->can('barrier.store') ?? false],
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateBarrierData $data, CreateBarrierAction $action, Request $request): RedirectResponse
    {
        $action->execute(
            data: $data,
            registeredBy: (int) $request->user()?->getAuthIdentifier(),
        );

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Barreira identificada com sucesso!');
    }

    public function show(Barrier $barrier, ShowBarrierQuery $query, Request $request): InertiaResponse
    {
        $barrier = $query->execute($barrier);

        return Inertia::render('InclusiveRadar/Barriers/Show', [
            'barrier' => $barrier,
            'display' => [
                'descriptionHtml' => RichTextSanitizer::sanitize($barrier->description ?: 'Sem descrição detalhada.'),
                'locationDetailsHtml' => $barrier->location_specific_details
                    ? RichTextSanitizer::sanitize($barrier->location_specific_details)
                    : null,
            ],
            'can' => [
                'edit' => $request->user()?->can('barrier.edit') ?? false,
                'destroy' => $request->user()?->can('barrier.destroy') ?? false,
                'pdf' => $request->user()?->can('barrier.pdf') ?? false,
                'inspectionShow' => $request->user()?->can('barrier.inspection.show') ?? false,
            ],
        ]);
    }

    public function edit(Barrier $barrier, Request $request, BarrierFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/Barriers/Edit',
            $form->forUpdate(
                barrier: $barrier,
                selectedInstitutionId: $request->old('institution_id')
                    ? (int) $request->old('institution_id')
                    : null,
            ) + ['canSubmit' => $request->user()?->can('barrier.update') ?? false],
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateBarrierData $data, Barrier $barrier, UpdateBarrierAction $action, Request $request): RedirectResponse
    {
        $action->execute(
            barrier: $barrier,
            data: $data,
            registeredBy: (int) $request->user()?->getAuthIdentifier(),
        );

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Barreira atualizada com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Barrier $barrier, DeleteBarrierAction $action): RedirectResponse
    {
        $action->execute($barrier);

        return redirect()
            ->route('inclusive-radar.barriers.index')
            ->with('success', 'Barreira removida com sucesso!');
    }

    public function showInspection(Barrier $barrier, Inspection $inspection, ShowBarrierInspectionQuery $query): InertiaResponse
    {
        $inspection = $query->execute($barrier, $inspection);

        return Inertia::render('InclusiveRadar/Barriers/Inspections/Show', [
            'barrier' => $barrier->loadMissing(['category']),
            'inspection' => $inspection,
            'descriptionHtml' => RichTextSanitizer::sanitize($inspection->description ?: 'Nenhum parecer técnico registrado.'),
        ]);
    }

    public function generatePdf(Barrier $barrier, BarrierPdfQuery $query): Response
    {
        $barrier = $query->execute($barrier);

        $pdf = Pdf::loadView('pages.inclusive-radar.barriers.pdf', compact('barrier'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => [public_path(), storage_path()],
            ]);

        PdfPageNumberer::apply($pdf);

        return $pdf->stream("Barreira_{$barrier->id}.pdf");
    }
}
