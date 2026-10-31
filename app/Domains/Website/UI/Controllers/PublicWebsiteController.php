<?php

declare(strict_types=1);

namespace App\Domains\Website\UI\Controllers;

use App\Domains\Inclusion\Domain\Enums\InstitutionalEventType;
use App\Domains\Inclusion\Domain\Models\InstitutionalEvent;
use App\Domains\Website\Application\Data\ListPublicPublicationsData;
use App\Domains\Website\Application\Queries\PublicPublicationsQuery;
use App\Domains\Website\Application\Queries\WebsiteContentQuery;
use App\Domains\Website\Domain\Models\WebsiteSetting;
use App\Domains\Website\UI\Presenters\WebsitePresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Site público white label, sem autenticação.
 */
final readonly class PublicWebsiteController
{
    public function __construct(private WebsitePresenter $presenter) {}

    public function home(WebsiteContentQuery $content): InertiaResponse
    {
        $setting = WebsiteSetting::current();
        $site = $this->presenter->site($setting);

        return Inertia::render('Website/Public/Home', [
            'site' => $site,
            'sections' => $this->presenter->sections($setting),
            'content' => $content->execute(),
            'seo' => [
                'title' => $site['name'],
                'description' => $site['tagline'],
            ],
        ]);
    }

    public function team(WebsiteContentQuery $content): InertiaResponse
    {
        $site = $this->presenter->site(WebsiteSetting::current());

        return Inertia::render('Website/Public/Team', [
            'site' => $site,
            'members' => $content->team(),
            'seo' => [
                'title' => "Equipe - {$site['name']}",
                'description' => "Conheça a equipe do {$site['name']}.",
            ],
        ]);
    }

    public function publications(
        Request $request,
        ListPublicPublicationsData $filters,
        PublicPublicationsQuery $query,
    ): InertiaResponse {
        $site = $this->presenter->site(WebsiteSetting::current());
        $types = [];

        foreach (InstitutionalEventType::options() as $value => $label) {
            $types[] = ['value' => $value, 'label' => $label];
        }

        return Inertia::render('Website/Public/Publications', [
            'site' => $site,
            'publications' => $query->execute($filters)->through(
                fn (InstitutionalEvent $event): array => $this->presenter->publicationCard($event),
            ),
            'filters' => $request->only(['q', 'type', 'category']),
            'types' => $types,
            'categories' => $query->categories(),
            'seo' => [
                'title' => "Publicações - {$site['name']}",
                'description' => "Notícias, eventos e comunicados do {$site['name']}.",
            ],
        ]);
    }

    public function publication(string $slug): InertiaResponse
    {
        $event = InstitutionalEvent::query()
            ->publiclyVisible()
            ->where('slug', $slug)
            ->with(['author', 'media', 'speakers.person', 'speakers.position'])
            ->firstOrFail();
        $site = $this->presenter->site(WebsiteSetting::current());
        $publication = $this->presenter->publicationDetail($event);

        $related = InstitutionalEvent::query()
            ->publiclyVisible()
            ->whereKeyNot($event->getKey())
            ->where('type', $event->type)
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(3)
            ->get()
            ->map(fn (InstitutionalEvent $item): array => $this->presenter->publicationCard($item))
            ->all();

        return Inertia::render('Website/Public/PublicationShow', [
            'site' => $site,
            'publication' => $publication,
            'related' => $related,
            'seo' => [
                'title' => "{$publication['seo_title']} - {$site['name']}",
                'description' => $publication['seo_description'] ?: $publication['summary'],
                'image' => $publication['cover_url'],
            ],
        ]);
    }
}
