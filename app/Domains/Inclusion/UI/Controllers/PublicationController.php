<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Publications\CreatePublicationAction;
use App\Domains\Inclusion\Application\Actions\Publications\DeletePublicationAction;
use App\Domains\Inclusion\Application\Actions\Publications\UpdatePublicationAction;
use App\Domains\Inclusion\Application\Data\Publications\CreatePublicationData;
use App\Domains\Inclusion\Application\Data\Publications\ListPublicationsData;
use App\Domains\Inclusion\Application\Data\Publications\UpdatePublicationData;
use App\Domains\Inclusion\Application\Queries\Publications\ListPublicationsQuery;
use App\Domains\Inclusion\Application\Queries\Publications\PublicationFormQuery;
use App\Domains\Inclusion\Application\Queries\Publications\PublicationPdfQuery;
use App\Domains\Inclusion\Application\Queries\Publications\ShowPublicationQuery;
use App\Domains\Inclusion\Domain\Enums\PublicationType;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPublication;
use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\UI\Presenters\PublicationPresenter;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
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
    public function __construct(private readonly PublicationPresenter $presenter) {}

    public function index(ListPublicationsData $filters, ListPublicationsQuery $query, Request $request): InertiaResponse
    {
        $publications = $query->execute($filters)->through(
            fn (Publication $publication): array => $this->presenter->publication($publication),
        );

        return Inertia::render(
            'Inclusion/Publications/Index',
            [
                'publications' => $publications,
                'filters' => $request->only(['title', 'is_active', 'type', 'per_page']),
                'types' => PublicationType::options(),
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
        $publication = $this->presenter->publication($query->execute($publication), detailed: true);
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
            'publication' => $this->presenter->publication($publication, detailed: true),
            'options' => $formQuery->execute(
                $request->user(),
                $publication->speakers->pluck('professional_id')->filter()->map(fn ($id): int => (int) $id)->values()->all(),
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
}
