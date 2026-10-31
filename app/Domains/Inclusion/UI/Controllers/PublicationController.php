<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Publications\CreatePublicationAction;
use App\Domains\Inclusion\Application\Actions\Publications\DeletePublicationAction;
use App\Domains\Inclusion\Application\Actions\Publications\UpdatePublicationAction;
use App\Domains\Inclusion\Application\Data\Publications\CreatePublicationData;
use App\Domains\Inclusion\Application\Data\Publications\ListPublicationsData;
use App\Domains\Inclusion\Application\Data\Publications\UpdatePublicationData;
use App\Domains\Inclusion\Application\Queries\Publications\PublicationFormQuery;
use App\Domains\Inclusion\Application\Queries\Publications\PublicationPdfQuery;
use App\Domains\Inclusion\Application\Queries\Publications\ListPublicationsQuery;
use App\Domains\Inclusion\Application\Queries\Publications\ShowPublicationQuery;
use App\Domains\Inclusion\Domain\Enums\PublicationType;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPublication;
use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\Domain\Models\PublicationMedia;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\Models\Tag;
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
 * Publicações do site (antiga agenda institucional): notícias, eventos,
 * comunicados, artigos e registros. Eventos guardam o período apenas como
 * registro exibido no site, sem lembretes automáticos.
 */
final class PublicationController
{
    public function index(ListPublicationsData $filters, ListPublicationsQuery $query, Request $request): InertiaResponse
    {
        $publications = $query->execute($filters)->through(
            fn (Publication $publication): array => $this->publicationPayload($publication),
        );

        return Inertia::render(
            'Inclusion/Publications/Index',
            [
                'publications' => $publications,
                'filters' => $request->only(['title', 'is_active', 'type', 'per_page']),
                'types' => $this->typeOptions(),
                'can' => [
                    'create' => $request->user()?->can('publication.create') ?? false,
                    'show' => $request->user()?->can('publication.show') ?? false,
                    'destroy' => $request->user()?->can('publication.destroy') ?? false,
                ],
            ],
        );
    }

    public function create(Request $request, PublicationFormQuery $formQuery): InertiaResponse
    {
        $backRoute = $request->query('back')
            ?? route('inclusive-radar.publications.index');

        return Inertia::render('Inclusion/Publications/Create', [
            'backRoute' => $backRoute,
            'options' => $formQuery->execute($request->user()),
            'can' => ['submit' => $request->user()?->can('publication.store') ?? false],
        ]);
    }

    /**
     * @throws InvalidPublication
     * @throws Throwable
     */
    public function store(CreatePublicationData $data, CreatePublicationAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('inclusive-radar.publications.index')
            ->with('success', 'Publicação criada com sucesso!');
    }

    public function show(Request $request, Publication $publication, ShowPublicationQuery $query): InertiaResponse
    {
        $publication = $this->publicationPayload($query->execute($publication), detailed: true);
        $backRoute = $request->query('back')
            ?? route('inclusive-radar.publications.index');

        return Inertia::render('Inclusion/Publications/Show', [
            'publication' => $publication,
            'backRoute' => $backRoute,
            'can' => [
                'edit' => $request->user()?->can('publication.edit') ?? false,
                'destroy' => $request->user()?->can('publication.destroy') ?? false,
                'pdf' => $request->user()?->can('publication.pdf') ?? false,
            ],
        ]);
    }

    public function edit(
        Publication $publication,
        Request $request,
        ShowPublicationQuery $query,
        PublicationFormQuery $formQuery,
    ): InertiaResponse {
        $publication = $query->execute($publication);

        return Inertia::render('Inclusion/Publications/Edit', [
            'publication' => $this->publicationPayload($publication, detailed: true),
            'options' => $formQuery->execute(
                $request->user(),
                $publication->speakers->modelKeys(),
                $publication->tags->modelKeys(),
            ),
            'can' => ['submit' => $request->user()?->can('publication.update') ?? false],
        ]);
    }

    /**
     * @throws InvalidPublication
     * @throws Throwable
     */
    public function update(UpdatePublicationData $data, Publication $publication, UpdatePublicationAction $action): RedirectResponse
    {
        $action->execute($publication, $data);

        return redirect()
            ->route('inclusive-radar.publications.index')
            ->with('success', 'Publicação atualizada com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Publication $publication, DeletePublicationAction $action): RedirectResponse
    {
        $action->execute($publication);

        return redirect()
            ->route('inclusive-radar.publications.index')
            ->with('success', 'Publicação removida com sucesso!');
    }

    public function generatePdf(Publication $publication, PublicationPdfQuery $query): Response
    {
        $publication = $query->execute($publication);

        $pdf = Pdf::loadView(
            'pages.inclusion.publications.pdf',
            compact('publication'),
        )
            ->setPaper('a4', 'portrait');

        PdfPageNumberer::apply($pdf);

        return $pdf->stream("Publicacao_{$publication->id}.pdf");
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function typeOptions(): array
    {
        $options = [];

        foreach (PublicationType::options() as $value => $label) {
            $options[] = ['value' => $value, 'label' => $label];
        }

        return $options;
    }

    /**
     * @return array<string, mixed>
     */
    private function publicationPayload(Publication $publication, bool $detailed = false): array
    {
        $type = $publication->type instanceof PublicationType
            ? $publication->type
            : PublicationType::EVENT;

        $payload = [
            'id' => $publication->id,
            'title' => $publication->title,
            'slug' => $publication->slug,
            'summary' => $publication->summary,
            'type' => $type->value,
            'type_label' => $type->label(),
            'tags' => $publication->tags
                ->map(fn (Tag $tag): array => ['id' => $tag->id, 'name' => $tag->name])
                ->values()
                ->all(),
            'has_schedule' => (bool) $publication->has_schedule,
            'description' => $publication->description,
            'start_date' => $publication->start_date?->format('Y-m-d'),
            'end_date' => $publication->end_date?->format('Y-m-d'),
            'start_date_label' => $publication->start_date?->format('d/m/Y') ?? '—',
            'end_date_label' => $publication->end_date?->format('d/m/Y') ?? '—',
            'start_time' => $publication->start_time?->format('H:i'),
            'end_time' => $publication->end_time?->format('H:i'),
            'location' => $publication->location,
            'organizer' => $publication->organizer,
            'audience' => $publication->audience,
            'is_active' => $publication->is_active,
            'is_featured' => (bool) $publication->is_featured,
            'published_at' => $publication->published_at?->format('Y-m-d'),
            'published_at_label' => $publication->published_at?->format('d/m/Y') ?? '—',
            'author_id' => $publication->author_id,
            'author_name' => $publication->author?->name,
            'schedule_status' => $publication->scheduleStatus(),
            'public_url' => $publication->slug && $publication->isPubliclyVisible()
                ? route('website.publications.show', $publication->slug)
                : null,
        ];

        if (! $detailed) {
            return $payload;
        }

        return [
            ...$payload,
            'description_html' => RichTextSanitizer::sanitize($publication->description ?? ''),
            'link_url' => $publication->link_url,
            'link_label' => $publication->link_label,
            'cover_url' => $publication->cover_path ? asset('storage/'.$publication->cover_path) : null,
            'cover_alt' => $publication->cover_alt,
            'seo_title' => $publication->seo_title,
            'seo_description' => $publication->seo_description,
            'gallery' => $publication->media
                ->map(fn (PublicationMedia $media): array => [
                    'id' => $media->id,
                    'url' => $media->url(),
                    'alt' => $media->alt_text,
                ])
                ->values()
                ->all(),
            'speakers' => $publication->speakers
                ->map(fn (Professional $professional): array => [
                    'id' => $professional->id,
                    'name' => $professional->person?->name,
                    'role' => $professional->position?->name,
                    'participation' => $professional->pivot?->participation,
                    'photo_url' => $professional->person?->photo_url,
                ])
                ->values()
                ->all(),
        ];
    }
}
