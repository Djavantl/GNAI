<?php

declare(strict_types=1);

namespace App\Domains\Website\UI\Presenters;

use App\Domains\Inclusion\Domain\Enums\PublicationType;
use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\Domain\Models\PublicationMedia;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Website\Domain\Models\WebsiteSetting;
use App\Domains\Website\Domain\Support\WebsiteDefaults;
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
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function publicationCard(Publication $event): array
    {
        $type = $event->type instanceof PublicationType ? $event->type : PublicationType::EVENT;
        $date = $event->published_at ?? $event->created_at;

        return [
            'id' => $event->id,
            'slug' => $event->slug,
            'url' => route('website.publications.show', $event->slug),
            'title' => $event->title,
            'summary' => $event->summary ?: Str::limit(
                trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $event->description))) ?? ''),
                220,
            ),
            'type' => $type->value,
            'type_label' => $type->label(),
            'category' => $event->category,
            'published_at' => $date?->toIso8601String(),
            'published_at_label' => $date ? $date->copy()->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y') : null,
            'cover_url' => $event->cover_path ? asset('storage/'.$event->cover_path) : null,
            'cover_alt' => $event->cover_alt ?? '',
            'is_featured' => (bool) $event->is_featured,
            'schedule' => $this->schedule($event),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function publicationDetail(Publication $event): array
    {
        return [
            ...$this->publicationCard($event),
            'content_html' => RichTextSanitizer::sanitize($event->description ?? ''),
            'author' => $event->author?->name,
            'organizer' => $event->organizer,
            'audience' => $event->audience,
            'link_url' => $event->link_url,
            'link_label' => $event->link_label,
            'seo_title' => $event->seo_title ?: $event->title,
            'seo_description' => $event->seo_description ?: ($event->summary ?? null),
            'gallery' => $event->media
                ->map(fn (PublicationMedia $media): array => [
                    'id' => $media->id,
                    'url' => $media->url(),
                    'alt' => $media->alt_text,
                ])
                ->values()
                ->all(),
            'speakers' => $event->speakers
                ->map(fn (Professional $professional): array => $this->teamMember($professional))
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
    private function schedule(Publication $event): ?array
    {
        $status = $event->scheduleStatus();

        if ($status === null) {
            return null;
        }

        $sameDay = $event->end_date === null || $event->start_date->isSameDay($event->end_date);

        return [
            'status' => $status,
            'status_label' => match ($status) {
                'upcoming' => 'Em breve',
                'ongoing' => 'Acontecendo agora',
                default => 'Encerrado',
            },
            'starts_at' => $event->startsAt()?->toIso8601String(),
            'day' => $event->start_date->format('d'),
            'month' => Str::upper($event->start_date->copy()->locale('pt_BR')->translatedFormat('M')),
            'date_label' => $sameDay
                ? $event->start_date->format('d/m/Y')
                : $event->start_date->format('d/m/Y').' a '.$event->end_date->format('d/m/Y'),
            'time_label' => trim(($event->start_time?->format('H:i') ?? '')
                .($event->end_time ? ' às '.$event->end_time->format('H:i') : '')),
            'location' => $event->location,
        ];
    }
}
