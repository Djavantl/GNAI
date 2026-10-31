<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Waitlists\CancelWaitlistAction;
use App\Domains\Inclusion\Application\Actions\Waitlists\CreateWaitlistAction;
use App\Domains\Inclusion\Application\Actions\Waitlists\DeleteWaitlistAction;
use App\Domains\Inclusion\Application\Actions\Waitlists\UpdateWaitlistAction;
use App\Domains\Inclusion\Application\Data\Waitlists\CreateWaitlistData;
use App\Domains\Inclusion\Application\Data\Waitlists\ListWaitlistsData;
use App\Domains\Inclusion\Application\Data\Waitlists\UpdateWaitlistData;
use App\Domains\Inclusion\Application\Queries\Waitlists\ListWaitlistsQuery;
use App\Domains\Inclusion\Application\Queries\Waitlists\ShowWaitlistQuery;
use App\Domains\Inclusion\Application\Queries\Waitlists\WaitlistFormQuery;
use App\Domains\Inclusion\Application\Queries\Waitlists\WaitlistPdfQuery;
use App\Domains\Inclusion\Domain\Models\Waitlist;
use App\Domains\Inclusion\UI\Presenters\WaitlistPresenter;
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
    public function index(
        ListWaitlistsData $filters,
        ListWaitlistsQuery $query,
        WaitlistPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        $waitlists = $query->execute($filters)->through(
            fn (Waitlist $waitlist): array => $presenter->waitlist($waitlist),
        );

        return Inertia::render(
            'Inclusion/Waitlists/Index',
            [
                'waitlists' => $waitlists,
                'filters' => $request->only(['item', 'student', 'professional', 'status', 'start_date', 'end_date', 'per_page']),
                'can' => [
                    'create' => $request->user()?->can('waitlist.create') ?? false,
                    'show' => $request->user()?->can('waitlist.show') ?? false,
                    'destroy' => $request->user()?->can('waitlist.destroy') ?? false,
                ],
            ],
        );
    }

    public function create(WaitlistFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Waitlists/Create',
            $form->forCreation(
                authUser: $request->user(),
            ) + [
                'can' => ['submit' => $request->user()?->can('waitlist.store') ?? false],
            ],
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

    public function show(
        Waitlist $waitlist,
        ShowWaitlistQuery $query,
        WaitlistPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        $waitlist = $query->execute($waitlist);

        return Inertia::render(
            'Inclusion/Waitlists/Show',
            [
                'waitlist' => $presenter->waitlist($waitlist, includeObservationHtml: true),
                'statusLabel' => $waitlist->status->label(),
                'statusColor' => $waitlist->status->color(),
                'canCancel' => $waitlist->status->canCancel(),
                'can' => [
                    'edit' => $request->user()?->can('waitlist.edit') ?? false,
                    'cancel' => $request->user()?->can('waitlist.cancel') ?? false,
                    'destroy' => $request->user()?->can('waitlist.destroy') ?? false,
                    'pdf' => $request->user()?->can('waitlist.pdf') ?? false,
                ],
            ],
        );
    }

    public function edit(
        Waitlist $waitlist,
        WaitlistFormQuery $form,
        WaitlistPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        $props = $form->forUpdate(
            waitlist: $waitlist,
            authUser: $request->user(),
        );
        $props['waitlist'] = $presenter->waitlist($waitlist);

        return Inertia::render(
            'Inclusion/Waitlists/Edit',
            $props + [
                'can' => ['submit' => $request->user()?->can('waitlist.update') ?? false],
            ],
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
            'pages.inclusion.waitlists.pdf',
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
