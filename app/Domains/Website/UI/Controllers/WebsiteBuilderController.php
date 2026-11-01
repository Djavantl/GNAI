<?php

declare(strict_types=1);

namespace App\Domains\Website\UI\Controllers;

use App\Domains\Website\Application\Actions\UpdateWebsiteAction;
use App\Domains\Website\Application\Data\UpdateWebsiteData;
use App\Domains\Website\Application\Queries\WebsiteContentQuery;
use App\Domains\Website\Domain\Enums\WebsiteSectionType;
use App\Domains\Website\Domain\Exceptions\InvalidWebsite;
use App\Domains\Website\Domain\Models\WebsiteSetting;
use App\Domains\Website\Domain\Support\WebsiteDefaults;
use App\Domains\Website\Domain\Support\PillarCardAppearance;
use App\Domains\Website\UI\Presenters\WebsitePresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

/**
 * Construtor da página inicial do site com prévia em tempo real.
 */
final readonly class WebsiteBuilderController
{
    public function __construct(private WebsitePresenter $presenter) {}

    public function edit(Request $request, WebsiteContentQuery $content): InertiaResponse
    {
        $setting = WebsiteSetting::current();

        return Inertia::render('Website/Builder/Edit', [
            'settings' => $this->presenter->settings($setting),
            'sections' => $this->presenter->sections($setting),
            'site' => $this->presenter->site($setting),
            'content' => $content->execute(),
            'sectionTypes' => WebsiteSectionType::options(),
            'pillarCardTemplate' => PillarCardAppearance::defaults(),
            'guidanceStepTemplate' => PillarCardAppearance::defaults('guidance'),
            'sectionTemplates' => collect(WebsiteDefaults::sections())
                ->mapWithKeys(fn (array $section): array => [$section['type'] => [...$section, 'image_url' => null]])
                ->all(),
            'publicUrl' => route('website.home'),
            'can' => [
                'submit' => $request->user()?->can('website.update') ?? false,
            ],
        ]);
    }

    /**
     * @throws InvalidWebsite
     * @throws Throwable
     */
    public function update(UpdateWebsiteData $data, UpdateWebsiteAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('website.builder.edit')
            ->with('success', 'Site atualizado com sucesso!');
    }
}
