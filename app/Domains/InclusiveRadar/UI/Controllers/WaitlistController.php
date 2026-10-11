<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\UI\Controllers;

use App\Domains\InclusiveRadar\Application\Actions\Waitlists\CancelWaitlistAction;
use App\Domains\InclusiveRadar\Application\Actions\Waitlists\CreateWaitlistAction;
use App\Domains\InclusiveRadar\Application\Actions\Waitlists\DeleteWaitlistAction;
use App\Domains\InclusiveRadar\Application\Actions\Waitlists\UpdateWaitlistAction;
use App\Domains\InclusiveRadar\Application\Data\Waitlists\CreateWaitlistData;
use App\Domains\InclusiveRadar\Application\Data\Waitlists\ListWaitlistsData;
use App\Domains\InclusiveRadar\Application\Data\Waitlists\UpdateWaitlistData;
use App\Domains\InclusiveRadar\Application\Queries\Waitlists\ListWaitlistsQuery;
use App\Domains\InclusiveRadar\Application\Queries\Waitlists\ShowWaitlistQuery;
use App\Domains\InclusiveRadar\Application\Queries\Waitlists\WaitlistFormQuery;
use App\Domains\InclusiveRadar\Application\Queries\Waitlists\WaitlistPdfQuery;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class WaitlistController
{
    public function index(ListWaitlistsData $filters, ListWaitlistsQuery $query, Request $request): InertiaResponse
    {
        $waitlists = $query->execute($filters);

        return Inertia::render(
            'InclusiveRadar/Waitlists/Index',
            compact('waitlists'),
        );
    }

    public function create(WaitlistFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/Waitlists/Create',
            $form->forCreation(
                authUser: $request->user(),
            ),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateWaitlistData $data, CreateWaitlistAction $action, Request $request): RedirectResponse
    {
        $action->execute(
            data: $data,
            registeredBy: (int) $request->user()?->getAuthIdentifier(),
        );

        return redirect()
            ->route('inclusive-radar.waitlists.index')
            ->with('success', 'Solicitação de fila criada com sucesso!');
    }

    public function show(Waitlist $waitlist, ShowWaitlistQuery $query, Request $request): InertiaResponse
    {
        $waitlist = $query->execute($waitlist);

        return Inertia::render(
            'InclusiveRadar/Waitlists/Show',
            [
                'waitlist' => $waitlist,
                'authUser' => $request->user(),
                'statusLabel' => $waitlist->status->label(),
                'statusColor' => $waitlist->status->color(),
                'canCancel' => $waitlist->status->canCancel(),
            ],
        );
    }

    public function edit(Waitlist $waitlist, WaitlistFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/Waitlists/Edit',
            $form->forUpdate(
                waitlist: $waitlist,
                authUser: $request->user(),
            ),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateWaitlistData $data, Waitlist $waitlist, UpdateWaitlistAction $action): RedirectResponse
    {
        $action->execute(
            waitlist: $waitlist,
            data: $data,
        );

        return redirect()
            ->route('inclusive-radar.waitlists.index')
            ->with('success', 'Fila atualizada com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Waitlist $waitlist, DeleteWaitlistAction $action): RedirectResponse
    {
        $action->execute($waitlist);

        return redirect()
            ->route('inclusive-radar.waitlists.index')
            ->with('success', 'Solicitação removida com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function cancel(Waitlist $waitlist, CancelWaitlistAction $action): RedirectResponse
    {
        $action->execute($waitlist);

        return redirect()
            ->back()
            ->with('success', 'Solicitação cancelada com sucesso!');
    }

    public function generatePdf(Waitlist $waitlist, WaitlistPdfQuery $query): Response
    {
        $waitlist = $query->execute($waitlist);

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.waitlists.pdf',
            [
                'waitlist' => $waitlist,
            ],
        )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => [public_path(), storage_path()],
            ]);

        PdfPageNumberer::apply($pdf);

        return $pdf->stream("Fila_Espera_{$waitlist->id}.pdf");
    }
}
