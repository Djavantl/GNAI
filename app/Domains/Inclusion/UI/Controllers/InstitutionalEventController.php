<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\InstitutionalEvents\CreateInstitutionalEventAction;
use App\Domains\Inclusion\Application\Actions\InstitutionalEvents\DeleteInstitutionalEventAction;
use App\Domains\Inclusion\Application\Actions\InstitutionalEvents\UpdateInstitutionalEventAction;
use App\Domains\Inclusion\Application\Data\InstitutionalEvents\CreateInstitutionalEventData;
use App\Domains\Inclusion\Application\Data\InstitutionalEvents\ListInstitutionalEventsData;
use App\Domains\Inclusion\Application\Data\InstitutionalEvents\UpdateInstitutionalEventData;
use App\Domains\Inclusion\Application\Queries\InstitutionalEvents\InstitutionalEventFormQuery;
use App\Domains\Inclusion\Application\Queries\InstitutionalEvents\InstitutionalEventPdfQuery;
use App\Domains\Inclusion\Application\Queries\InstitutionalEvents\ListInstitutionalEventsQuery;
use App\Domains\Inclusion\Application\Queries\InstitutionalEvents\ShowInstitutionalEventQuery;
use App\Domains\Inclusion\Domain\Enums\InstitutionalEventType;
use App\Domains\Inclusion\Domain\Exceptions\InvalidInstitutionalEvent;
use App\Domains\Inclusion\Domain\Models\InstitutionalEvent;
use App\Domains\Inclusion\Domain\Models\InstitutionalEventMedia;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

/**
 * Publicações do site (antiga agenda institucional). As rotas e permissões
 * institutional-event.* foram mantidas para preservar lembretes, relatórios,
 * notificações já enviadas e perfis de acesso existentes.
 */
final class InstitutionalEventController
{
    public function index(ListInstitutionalEventsData $filters, ListInstitutionalEventsQuery $query, Request $request): InertiaResponse
    {
        $events = $query->execute($filters)->through(
            fn (InstitutionalEvent $event): array => $this->eventPayload($event),
        );

        return Inertia::render(
            'Inclusion/InstitutionalEvents/Index',
            [
                'events' => $events,
                'filters' => $request->only(['title', 'is_active', 'type', 'per_page']),
                'types' => $this->typeOptions(),
                'can' => [
                    'create' => $request->user()?->can('institutional-event.create') ?? false,
                    'show' => $request->user()?->can('institutional-event.show') ?? false,
                    'destroy' => $request->user()?->can('institutional-event.destroy') ?? false,
                ],
            ],
        );
    }

    public function create(Request $request, InstitutionalEventFormQuery $formQuery): InertiaResponse
    {
        $backRoute = $request->query('back')
            ?? route('inclusive-radar.institutional-events.index');

        return Inertia::render('Inclusion/InstitutionalEvents/Create', [
            'backRoute' => $backRoute,
            'options' => $formQuery->execute($request->user()),
            'can' => ['submit' => $request->user()?->can('institutional-event.store') ?? false],
        ]);
    }

    /**
     * @throws InvalidInstitutionalEvent
     * @throws Throwable
     */
    public function store(CreateInstitutionalEventData $data, CreateInstitutionalEventAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('inclusive-radar.institutional-events.index')
            ->with('success', 'Publicação criada com sucesso!');
    }

    public function show(Request $request, InstitutionalEvent $event, ShowInstitutionalEventQuery $query): InertiaResponse
    {
        $event = $this->eventPayload($query->execute($event), detailed: true);
        $backRoute = $request->query('back')
            ?? route('inclusive-radar.institutional-events.index');

        return Inertia::render('Inclusion/InstitutionalEvents/Show', [
            'event' => $event,
            'backRoute' => $backRoute,
            'can' => [
                'edit' => $request->user()?->can('institutional-event.edit') ?? false,
                'destroy' => $request->user()?->can('institutional-event.destroy') ?? false,
                'pdf' => $request->user()?->can('institutional-event.pdf') ?? false,
            ],
        ]);
    }

    public function edit(
        InstitutionalEvent $event,
        Request $request,
        ShowInstitutionalEventQuery $query,
        InstitutionalEventFormQuery $formQuery,
    ): InertiaResponse {
        $event = $query->execute($event);

        return Inertia::render('Inclusion/InstitutionalEvents/Edit', [
            'event' => $this->eventPayload($event, detailed: true),
            'options' => $formQuery->execute(
                $request->user(),
                $event->speakers->modelKeys(),
            ),
            'can' => ['submit' => $request->user()?->can('institutional-event.update') ?? false],
        ]);
    }

    /**
     * @throws InvalidInstitutionalEvent
     * @throws Throwable
     */
    public function update(UpdateInstitutionalEventData $data, InstitutionalEvent $event, UpdateInstitutionalEventAction $action): RedirectResponse
    {
        $action->execute($event, $data);

        return redirect()
            ->route('inclusive-radar.institutional-events.index')
            ->with('success', 'Publicação atualizada com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function destroy(InstitutionalEvent $event, DeleteInstitutionalEventAction $action): RedirectResponse
    {
        $action->execute($event);

        return redirect()
            ->route('inclusive-radar.institutional-events.index')
            ->with('success', 'Publicação removida com sucesso!');
    }

    public function generatePdf(InstitutionalEvent $event, InstitutionalEventPdfQuery $query): Response
    {
        $event = $query->execute($event);

        $pdf = Pdf::loadView(
            'pages.inclusion.institutional-events.pdf',
            compact('event'),
        )
            ->setPaper('a4', 'portrait');

        PdfPageNumberer::apply($pdf);

        return $pdf->stream("Publicacao_{$event->id}.pdf");
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function typeOptions(): array
    {
        $options = [];

        foreach (InstitutionalEventType::options() as $value => $label) {
            $options[] = ['value' => $value, 'label' => $label];
        }

        return $options;
    }

    /**
     * @return array<string, mixed>
     */
    private function eventPayload(InstitutionalEvent $event, bool $detailed = false): array
    {
        $type = $event->type instanceof InstitutionalEventType
            ? $event->type
            : InstitutionalEventType::EVENT;

        $payload = [
            'id' => $event->id,
            'title' => $event->title,
            'slug' => $event->slug,
            'summary' => $event->summary,
            'type' => $type->value,
            'type_label' => $type->label(),
            'category' => $event->category,
            'has_schedule' => (bool) $event->has_schedule,
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
            'is_featured' => (bool) $event->is_featured,
            'published_at' => $event->published_at?->format('Y-m-d'),
            'published_at_label' => $event->published_at?->format('d/m/Y') ?? '—',
            'author_id' => $event->author_id,
            'author_name' => $event->author?->name,
            'schedule_status' => $event->scheduleStatus(),
            'public_url' => $event->slug && $event->isPubliclyVisible()
                ? route('website.publications.show', $event->slug)
                : null,
        ];

        if (! $detailed) {
            return $payload;
        }

        return [
            ...$payload,
            'description_html' => RichTextSanitizer::sanitize($event->description ?? ''),
            'link_url' => $event->link_url,
            'link_label' => $event->link_label,
            'cover_url' => $event->cover_path ? asset('storage/'.$event->cover_path) : null,
            'cover_alt' => $event->cover_alt,
            'seo_title' => $event->seo_title,
            'seo_description' => $event->seo_description,
            'gallery' => $event->media
                ->map(fn (InstitutionalEventMedia $media): array => [
                    'id' => $media->id,
                    'url' => $media->url(),
                    'alt' => $media->alt_text,
                ])
                ->values()
                ->all(),
            'speakers' => $event->speakers
                ->map(fn (Professional $professional): array => [
                    'id' => $professional->id,
                    'name' => $professional->person?->name,
                    'role' => $professional->position?->name,
                    'photo_url' => $professional->person?->photo_url,
                ])
                ->values()
                ->all(),
        ];
    }
}
