<?php

declare(strict_types=1);

namespace App\Domains\Website\Application\Queries;

use App\Domains\Inclusion\Domain\Enums\ProfessionalStatus;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Website\UI\Presenters\WebsitePresenter;

/**
 * Conteúdo dinâmico das seções da página inicial. Retorna até 12 itens de
 * cada tipo; a seção decide quantos exibir conforme o limite configurado.
 */
final readonly class WebsiteContentQuery
{
    public function __construct(private WebsitePresenter $presenter) {}

    /**
     * @return array{team: list<array<string, mixed>>, events: list<array<string, mixed>>, publications: list<array<string, mixed>>}
     */
    public function execute(): array
    {
        $events = Publication::query()
            ->publiclyVisible()
            ->where('has_schedule', true)
            ->whereNotNull('start_date')
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->with('tags')
            ->limit(12)
            ->get();

        $publications = Publication::query()
            ->publiclyVisible()
            ->orderByDesc('is_featured')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->orderByDesc('id')
            ->with('tags')
            ->limit(12)
            ->get();

        return [
            'team' => array_slice($this->team(), 0, 12),
            'events' => $events->map(fn (Publication $publication): array => $this->presenter->publicationCard($publication))->all(),
            'publications' => $publications->map(fn (Publication $publication): array => $this->presenter->publicationCard($publication))->all(),
        ];
    }

    /**
     * Profissionais ativos marcados para aparecer no site, em ordem alfabética.
     *
     * @return list<array<string, mixed>>
     */
    public function team(): array
    {
        return Professional::query()
            ->with(['person', 'position'])
            ->where('show_on_website', true)
            ->where('status', ProfessionalStatus::ACTIVE->value)
            ->get()
            ->sortBy(fn (Professional $professional): string => (string) $professional->person?->name, SORT_NATURAL | SORT_FLAG_CASE)
            ->map(fn (Professional $professional): array => $this->presenter->teamMember($professional))
            ->values()
            ->all();
    }
}
