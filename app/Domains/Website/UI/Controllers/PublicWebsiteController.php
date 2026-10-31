<?php

declare(strict_types=1);

namespace App\Domains\Website\UI\Controllers;

use App\Domains\Inclusion\Domain\Enums\PublicationType;
use App\Domains\Inclusion\Domain\Models\Publication;
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

        return Inertia::render('Website/Public/Publications', [
            'site' => $site,
            'publications' => $query->execute($filters)->through(
                fn (Publication $publication): array => $this->presenter->publicationCard($publication),
            ),
            'filters' => $request->only(['q', 'type', 'tag']),
            'types' => PublicationType::options(),
            'tags' => $query->tags(),
            'seo' => [
                'title' => "Publicações - {$site['name']}",
                'description' => "Notícias, eventos e comunicados do {$site['name']}.",
            ],
        ]);
    }

    public function publication(string $slug, PublicPublicationsQuery $query): InertiaResponse
    {
        $publication = $query->findVisible($slug);
        $site = $this->presenter->site(WebsiteSetting::current());
        $detail = $this->presenter->publicationDetail($publication);
        $related = $query->related($publication)
            ->map(fn (Publication $item): array => $this->presenter->publicationCard($item))
            ->all();

        return Inertia::render('Website/Public/PublicationShow', [
            'site' => $site,
            'publication' => $detail,
            'related' => $related,
            'seo' => [
                'title' => "{$detail['seo_title']} - {$site['name']}",
                'description' => $detail['seo_description'] ?: $detail['summary'],
                'image' => $detail['cover_url'],
            ],
        ]);
    }
}
