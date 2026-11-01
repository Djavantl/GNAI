<?php

declare(strict_types=1);

namespace App\Domains\Website\UI\Presenters;

use App\Domains\Inclusion\Domain\Enums\PublicationType;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\Domain\Models\PublicationMedia;
use App\Domains\Inclusion\Domain\Models\PublicationSpeaker;
use App\Domains\Website\Domain\Models\WebsiteSetting;
use App\Domains\Website\Domain\Support\WebsiteDefaults;
use App\Domains\Website\Domain\Support\AboutAppearance;
use App\Domains\Website\Domain\Support\GuidanceAppearance;
use App\Domains\Website\Domain\Support\TeamAppearance;
use App\Domains\Website\Domain\Support\ContactAppearance;
use App\Domains\Website\Domain\Support\PublicationsAppearance;
use App\Domains\Website\Domain\Support\PillarCardAppearance;
use App\Domains\Website\Domain\Support\PillarsAppearance;
use App\Domains\Website\Domain\Support\HeroAppearance;
use App\Domains\Website\Infrastructure\Storage\WebsiteMediaStorage;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Illuminate\Support\Str;

/**
 * Formata os dados consumidos pelo site público e pela prévia do construtor.
 */
final class WebsitePresenter
{
    /**
     * @return array<string, mixed>
     */
    public function site(WebsiteSetting $setting): array
    {
        $social = is_array($setting->social_links) ? $setting->social_links : [];

        return [
            'name' => $setting->site_name ?: WebsiteDefaults::identity()['site_name'],
            'tagline' => $setting->tagline,
            'logo_url' => WebsiteMediaStorage::url($setting->logo_path),
            'primary_color' => $setting->primary_color ?: WebsiteDefaults::PRIMARY_COLOR,
            'contact_email' => $setting->contact_email,
            'contact_phone' => $setting->contact_phone,
            'address' => $setting->address,
            'social' => [
                'instagram' => $social['instagram'] ?? null,
                'facebook' => $social['facebook'] ?? null,
                'youtube' => $social['youtube'] ?? null,
                'website' => $social['website'] ?? null,
            ],
            'footer_text' => $setting->footer_text,
            'login_url' => route('login'),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function sections(WebsiteSetting $setting): array
    {
        return collect($setting->sections ?? [])
            ->filter(fn ($section): bool => is_array($section) && isset($section['id'], $section['type']))
            ->map(fn (array $section): array => [
                ...WebsiteDefaults::section((string) $section['id'], (string) $section['type']),
                ...$section,
                'image_url' => WebsiteMediaStorage::url($section['image_path'] ?? null),
                ...($section['type'] === 'guidance' ? [
                    'guidance' => GuidanceAppearance::normalize((array) ($section['guidance'] ?? [])),
                    'items' => array_map(fn (array $item): array => [
                        ...$item,
                        'appearance' => PillarCardAppearance::normalize((array) ($item['appearance'] ?? []), 'guidance'),
                    ], $section['items'] ?? []),
                ] : []),
                ...(in_array($section['type'], PillarsAppearance::SECTION_TYPES, true) ? [
                    'pillars' => PillarsAppearance::normalize((array) ($section['pillars'] ?? []), $section['type']),
                    'items' => array_map(fn (array $item): array => [
                        ...$item,
                        'appearance' => PillarCardAppearance::normalize((array) ($item['appearance'] ?? [])),
                    ], $section['items'] ?? []),
                ] : []),
                ...($section['type'] === 'publications' ? ['publications' => PublicationsAppearance::normalize((array) ($section['publications'] ?? []))] : []),
                ...($section['type'] === 'events' ? ['events' => PublicationsAppearance::normalize((array) ($section['events'] ?? []))] : []),
                ...($section['type'] === 'team' ? ['team' => TeamAppearance::normalize((array) ($section['team'] ?? []))] : []),
                ...($section['type'] === 'contact' ? ['contact' => ContactAppearance::normalize((array) ($section['contact'] ?? []))] : []),
                ...($section['type'] === 'about' ? ['about' => AboutAppearance::normalize((array) ($section['about'] ?? []))] : []),
                ...($section['type'] === 'hero' ? [
                    'hero' => HeroAppearance::normalize((array) ($section['hero'] ?? [])),
                    'slides' => array_map(fn (array $slide): array => [
                        ...$slide,
                        'image_url' => WebsiteMediaStorage::url($slide['image_path'] ?? null),
                    ], (array) ($section['slides'] ?? [])),
                ] : []),
            ])
            ->values()
            ->all();
    }

    /**
     * Valores do formulário do construtor, com as redes sociais separadas por campo.
     *
     * @return array<string, mixed>
     */
    public function settings(WebsiteSetting $setting): array
    {
        $social = is_array($setting->social_links) ? $setting->social_links : [];

        return [
            'site_name' => $setting->site_name,
            'tagline' => $setting->tagline,
            'primary_color' => $setting->primary_color,
            'logo_url' => $this->site($setting)['logo_url'],
            'contact_email' => $setting->contact_email,
            'contact_phone' => $setting->contact_phone,
            'address' => $setting->address,
            'instagram_url' => $social['instagram'] ?? null,
            'facebook_url' => $social['facebook'] ?? null,
            'youtube_url' => $social['youtube'] ?? null,
            'website_url' => $social['website'] ?? null,
            'footer_text' => $setting->footer_text,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function publicationCard(Publication $publication): array
    {
        $type = $publication->type instanceof PublicationType ? $publication->type : PublicationType::EVENT;
        $date = $publication->published_at ?? $publication->created_at;

        return [
            'id' => $publication->id,
            'slug' => $publication->slug,
            'url' => route('website.publications.show', $publication->slug),
            'title' => $publication->title,
            'summary' => $publication->summary ?: Str::limit(
                trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $publication->description))) ?? ''),
                220,
            ),
            'type' => $type->value,
            'type_label' => $type->label(),
            'tags' => $publication->tags->where('is_active', true)->pluck('name')->values()->all(),
            'published_at' => $date?->toIso8601String(),
            'published_at_label' => $date ? $date->copy()->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y') : null,
            'cover_url' => $publication->cover_path ? asset('storage/'.$publication->cover_path) : null,
            'cover_alt' => $publication->cover_alt ?? '',
            'is_featured' => (bool) $publication->is_featured,
            'schedule' => $this->schedule($publication),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function publicationDetail(Publication $publication): array
    {
        return [
            ...$this->publicationCard($publication),
            'content_html' => RichTextSanitizer::sanitize($publication->description ?? ''),
            'author' => $publication->author?->name,
            'organizer' => $publication->organizer,
            'audience' => $publication->audience,
            'link_url' => $publication->link_url,
            'link_label' => $publication->link_label,
            'seo_title' => $publication->seo_title ?: $publication->title,
            'seo_description' => $publication->seo_description ?: ($publication->summary ?? null),
            'gallery' => $publication->media
                ->map(fn (PublicationMedia $media): array => [
                    'id' => $media->id,
                    'url' => $media->url(),
                    'alt' => $media->alt_text,
                ])
                ->values()
                ->all(),
            'speakers' => $publication->speakers
                ->map(fn (PublicationSpeaker $speaker): array => [
                    'id' => $speaker->id,
                    'name' => $speaker->displayName(),
                    'role' => collect([$speaker->participation, $speaker->professional?->position?->name])
                        ->filter()
                        ->implode(' · ') ?: null,
                    'photo_url' => $speaker->professional?->person?->photo_url ?? asset('images/default-user.jpg'),
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function teamMember(Professional $professional): array
    {
        return [
            'id' => $professional->id,
            'name' => $professional->person?->name ?? 'Profissional',
            'role' => $professional->position?->name,
            'photo_url' => $professional->person?->photo_url ?? asset('images/default-user.jpg'),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function schedule(Publication $publication): ?array
    {
        $status = $publication->scheduleStatus();

        if ($status === null) {
            return null;
        }

        $sameDay = $publication->end_date === null || $publication->start_date->isSameDay($publication->end_date);

        return [
            'status' => $status,
            'status_label' => match ($status) {
                'upcoming' => 'Em breve',
                'ongoing' => 'Acontecendo agora',
                default => 'Encerrado',
            },
            'starts_at' => $publication->startsAt()?->toIso8601String(),
            'day' => $publication->start_date->format('d'),
            'month' => Str::upper($publication->start_date->copy()->locale('pt_BR')->translatedFormat('M')),
            'date_label' => $sameDay
                ? $publication->start_date->format('d/m/Y')
                : $publication->start_date->format('d/m/Y').' a '.$publication->end_date->format('d/m/Y'),
            'time_label' => trim(($publication->start_time?->format('H:i') ?? '')
                .($publication->end_time ? ' às '.$publication->end_time->format('H:i') : '')),
            'location' => $publication->location,
        ];
    }
}
