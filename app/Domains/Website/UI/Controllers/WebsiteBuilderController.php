<?php

declare(strict_types=1);

namespace App\Domains\Website\UI\Controllers;

use App\Domains\Website\Application\Actions\UpdateWebsiteAction;
use App\Domains\Website\Application\Data\UpdateWebsiteData;
use App\Domains\Website\Application\Queries\WebsiteContentQuery;
use App\Domains\Website\Domain\Enums\WebsiteSectionType;
use App\Domains\Website\Domain\Exceptions\InvalidWebsite;
use App\Domains\Website\Domain\Models\WebsiteSetting;
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
        $social = is_array($setting->social_links) ? $setting->social_links : [];

        return Inertia::render('Website/Builder/Edit', [
            'settings' => [
                'site_name' => $setting->site_name,
                'tagline' => $setting->tagline,
                'primary_color' => $setting->primary_color,
                'logo_url' => $this->presenter->site($setting)['logo_url'],
                'contact_email' => $setting->contact_email,
                'contact_phone' => $setting->contact_phone,
                'address' => $setting->address,
                'instagram_url' => $social['instagram'] ?? null,
                'facebook_url' => $social['facebook'] ?? null,
                'youtube_url' => $social['youtube'] ?? null,
                'website_url' => $social['website'] ?? null,
                'footer_text' => $setting->footer_text,
            ],
            'sections' => $this->presenter->sections($setting),
            'site' => $this->presenter->site($setting),
            'content' => $content->execute(),
            'sectionTypes' => WebsiteSectionType::options(),
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
