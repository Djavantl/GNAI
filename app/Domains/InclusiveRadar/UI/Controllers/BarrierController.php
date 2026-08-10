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
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

final class BarrierController
{
    public function index(ListBarriersData $filters, ListBarriersQuery $query, Request $request): View
    {
        $barriers = $query->execute($filters);

        if ($request->ajax()) {
            return view('pages.inclusive-radar.barriers.partials.table', compact('barriers'));
        }

        return view('pages.inclusive-radar.barriers.index', compact('barriers'));
    }

    public function create(Request $request, BarrierFormQuery $form): View
    {
        return view(
            'pages.inclusive-radar.barriers.create',
            $form->forCreation(
                selectedInstitutionId: $request->old('institution_id')
                    ? (int) $request->old('institution_id')
                    : null,
            ),
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

    public function show(Barrier $barrier, ShowBarrierQuery $query): View
    {
        $barrier = $query->execute($barrier);

        return view('pages.inclusive-radar.barriers.show', compact('barrier'));
    }

    public function edit(Barrier $barrier, Request $request, BarrierFormQuery $form): View
    {
        return view(
            'pages.inclusive-radar.barriers.edit',
            $form->forUpdate(
                barrier: $barrier,
                selectedInstitutionId: $request->old('institution_id')
                    ? (int) $request->old('institution_id')
                    : null,
            ),
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

    public function showInspection(Barrier $barrier, Inspection $inspection, ShowBarrierInspectionQuery $query): View
    {
        $inspection = $query->execute($barrier, $inspection);

        return view('pages.inclusive-radar.barriers.inspections.show', [
            'barrier' => $barrier,
            'inspection' => $inspection,
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
