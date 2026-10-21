<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\UI\Controllers;

use App\Domains\InclusiveRadar\Application\Actions\InstitutionalEvents\CreateInstitutionalEventAction;
use App\Domains\InclusiveRadar\Application\Actions\InstitutionalEvents\DeleteInstitutionalEventAction;
use App\Domains\InclusiveRadar\Application\Actions\InstitutionalEvents\UpdateInstitutionalEventAction;
use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\CreateInstitutionalEventData;
use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\ListInstitutionalEventsData;
use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\UpdateInstitutionalEventData;
use App\Domains\InclusiveRadar\Application\Queries\InstitutionalEvents\InstitutionalEventPdfQuery;
use App\Domains\InclusiveRadar\Application\Queries\InstitutionalEvents\ListInstitutionalEventsQuery;
use App\Domains\InclusiveRadar\Application\Queries\InstitutionalEvents\ShowInstitutionalEventQuery;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitutionalEvent;
use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class InstitutionalEventController
{
    public function index(ListInstitutionalEventsData $filters, ListInstitutionalEventsQuery $query, Request $request): InertiaResponse
    {
        $events = $query->execute($filters)->through(
            fn (InstitutionalEvent $event): array => $this->eventPayload($event),
        );

        return Inertia::render(
            'InclusiveRadar/InstitutionalEvents/Index',
            [
                'events' => $events,
                'filters' => $request->only(['title', 'is_active', 'per_page']),
                'can' => [
                    'create' => $request->user()?->can('institutional-event.create') ?? false,
                    'show' => $request->user()?->can('institutional-event.show') ?? false,
                    'destroy' => $request->user()?->can('institutional-event.destroy') ?? false,
                ],
            ],
        );
    }

    public function create(Request $request): InertiaResponse
    {
        $backRoute = $request->query('back')
            ?? route('inclusive-radar.institutional-events.index');

        return Inertia::render('InclusiveRadar/InstitutionalEvents/Create', [
            'backRoute' => $backRoute,
            'can' => ['submit' => $request->user()?->can('institutional-event.store') ?? false],
        ]);
    }

    /**
     * @throws InvalidInstitutionalEvent
     */
    public function store(CreateInstitutionalEventData $data, CreateInstitutionalEventAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('inclusive-radar.institutional-events.index')
            ->with('success', 'Evento criado com sucesso!');
    }

    public function show(Request $request, InstitutionalEvent $event, ShowInstitutionalEventQuery $query): InertiaResponse
    {
        $event = $this->eventPayload($query->execute($event), includeDescriptionHtml: true);
        $backRoute = $request->query('back')
            ?? route('inclusive-radar.institutional-events.index');

        return Inertia::render('InclusiveRadar/InstitutionalEvents/Show', [
            'event' => $event,
            'backRoute' => $backRoute,
            'can' => [
                'edit' => $request->user()?->can('institutional-event.edit') ?? false,
                'destroy' => $request->user()?->can('institutional-event.destroy') ?? false,
                'pdf' => $request->user()?->can('institutional-event.pdf') ?? false,
            ],
        ]);
    }

    public function edit(InstitutionalEvent $event, Request $request): InertiaResponse
    {
        return Inertia::render('InclusiveRadar/InstitutionalEvents/Edit', [
            'event' => $this->eventPayload($event),
            'can' => ['submit' => $request->user()?->can('institutional-event.update') ?? false],
        ]);
    }

    /**
     * @throws InvalidInstitutionalEvent
     */
    public function update(UpdateInstitutionalEventData $data, InstitutionalEvent $event, UpdateInstitutionalEventAction $action): RedirectResponse
    {
        $action->execute($event, $data);

        return redirect()
            ->route('inclusive-radar.institutional-events.index')
            ->with('success', 'Evento atualizado com sucesso!');
    }

    /**
     * @throws \Throwable
     */
    public function destroy(InstitutionalEvent $event, DeleteInstitutionalEventAction $action): RedirectResponse
    {
        $action->execute($event);

        return redirect()
            ->route('inclusive-radar.institutional-events.index')
            ->with('success', 'Evento removido com sucesso!');
    }

    public function generatePdf(InstitutionalEvent $event, InstitutionalEventPdfQuery $query): Response
    {
        $event = $query->execute($event);

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.institutional-events.pdf',
            compact('event'),
        )
            ->setPaper('a4', 'portrait');

        PdfPageNumberer::apply($pdf);

        return $pdf->stream("Evento_{$event->id}.pdf");
    }

    /**
     * @return array<string, mixed>
     */
    private function eventPayload(InstitutionalEvent $event, bool $includeDescriptionHtml = false): array
    {
        $payload = [
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'start_date' => $event->start_date?->format('Y-m-d'),
            'end_date' => $event->end_date?->format('Y-m-d'),
            'start_date_label' => $event->start_date?->format('d/m/Y') ?? '—',
            'end_date_label' => $event->end_date?->format('d/m/Y') ?? '—',
            'start_time' => $event->start_time?->format('H:i'),
            'end_time' => $event->end_time?->format('H:i'),
            'location' => $event->location,
            'organizer' => $event->organizer,
            'audience' => $event->audience,
            'is_active' => $event->is_active,
        ];

        if ($includeDescriptionHtml) {
            $payload['description_html'] = RichTextSanitizer::sanitize($event->description ?? '');
        }

        return $payload;
    }
}
