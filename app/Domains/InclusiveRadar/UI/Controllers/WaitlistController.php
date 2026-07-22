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
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

final class WaitlistController extends Controller
{
    public function index(ListWaitlistsData $filters, ListWaitlistsQuery $query, Request $request): View
    {
        $waitlists = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.waitlists.partials.table',
                compact('waitlists'),
            );
        }

        return view(
            'pages.inclusive-radar.waitlists.index',
            compact('waitlists'),
        );
    }

    public function create(WaitlistFormQuery $form, Request $request): View
    {
        return view(
            'pages.inclusive-radar.waitlists.create',
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

    public function show(Waitlist $waitlist, ShowWaitlistQuery $query, Request $request): View
    {
        $waitlist = $query->execute($waitlist);

        return view(
            'pages.inclusive-radar.waitlists.show',
            [
                'waitlist' => $waitlist,
                'authUser' => $request->user(),
                'statusLabel' => $waitlist->status->label(),
                'statusColor' => $waitlist->status->color(),
                'canCancel' => $waitlist->status->canCancel(),
            ],
        );
    }

    public function edit(Waitlist $waitlist, WaitlistFormQuery $form, Request $request): View
    {
        return view(
            'pages.inclusive-radar.waitlists.edit',
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
                'enable_php' => true,
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => [public_path(), storage_path()],
            ]);

        return $pdf->stream("Fila_Espera_{$waitlist->id}.pdf");
    }
}
