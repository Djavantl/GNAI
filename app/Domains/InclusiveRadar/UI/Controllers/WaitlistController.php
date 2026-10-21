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
use App\Shared\Infrastructure\Security\RichTextSanitizer;
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
        $waitlists = $query->execute($filters)->through(
            fn (Waitlist $waitlist): array => $this->waitlistPayload($waitlist),
        );

        return Inertia::render(
            'InclusiveRadar/Waitlists/Index',
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
            'InclusiveRadar/Waitlists/Create',
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

    public function show(Waitlist $waitlist, ShowWaitlistQuery $query, Request $request): InertiaResponse
    {
        $waitlist = $query->execute($waitlist);

        return Inertia::render(
            'InclusiveRadar/Waitlists/Show',
            [
                'waitlist' => $this->waitlistPayload($waitlist, includeObservationHtml: true),
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

    public function edit(Waitlist $waitlist, WaitlistFormQuery $form, Request $request): InertiaResponse
    {
        $props = $form->forUpdate(
            waitlist: $waitlist,
            authUser: $request->user(),
        );
        $props['waitlist'] = $this->waitlistPayload($waitlist);

        return Inertia::render(
            'InclusiveRadar/Waitlists/Edit',
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

    /**
     * @return array<string, mixed>
     */
    private function waitlistPayload(Waitlist $waitlist, bool $includeObservationHtml = false): array
    {
        $waitlist->loadMissing([
            'waitlistable',
            'student:id,person_id,registration',
            'student.person:id,name',
            'professional:id,person_id',
            'professional.person:id,name',
            'user:id,name',
        ]);

        $payload = [
            'id' => $waitlist->id,
            'student_id' => $waitlist->student_id,
            'professional_id' => $waitlist->professional_id,
            'waitlistable_id' => $waitlist->waitlistable_id,
            'waitlistable_type' => $waitlist->waitlistable_type,
            'item_name' => $waitlist->waitlistable?->name ?? $waitlist->waitlistable?->title ?? 'Item removido',
            'asset_code' => $waitlist->waitlistable?->asset_code,
            'student' => $waitlist->student ? [
                'name' => $waitlist->student->person?->name ?? 'Sem nome',
                'registration' => $waitlist->student->registration,
            ] : null,
            'professional' => $waitlist->professional ? [
                'name' => $waitlist->professional->person?->name ?? 'Sem nome',
            ] : null,
            'user' => $waitlist->user?->name ?? '—',
            'requested_at' => $waitlist->requested_at?->format('Y-m-d\TH:i:s'),
            'requested_at_label' => $waitlist->requested_at?->format('d/m/Y H:i') ?? '—',
            'status' => $waitlist->status->value,
            'status_label' => $waitlist->status->label(),
            'status_color' => $waitlist->status->color(),
            'observation' => $waitlist->observation,
        ];

        if ($includeObservationHtml) {
            $payload['observation_html'] = RichTextSanitizer::sanitize($waitlist->observation ?? '');
        }

        return $payload;
    }
}
