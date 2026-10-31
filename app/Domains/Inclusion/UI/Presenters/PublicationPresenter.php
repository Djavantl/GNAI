<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Presenters;

use App\Domains\Inclusion\Domain\Enums\PublicationType;
use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\Domain\Models\PublicationMedia;
use App\Domains\Inclusion\Domain\Models\PublicationSpeaker;
use App\Domains\Inclusion\Domain\Models\Tag;
use App\Shared\Infrastructure\Security\RichTextSanitizer;

final class PublicationPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function publication(Publication $publication, bool $detailed = false): array
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
            'start_date_label' => $publication->start_date?->format('d/m/Y') ?? 'Não informado',
            'end_date_label' => $publication->end_date?->format('d/m/Y') ?? 'Não informado',
            'start_time' => $publication->start_time?->format('H:i'),
            'end_time' => $publication->end_time?->format('H:i'),
            'location' => $publication->location,
            'organizer' => $publication->organizer,
            'audience' => $publication->audience,
            'is_active' => $publication->is_active,
            'is_featured' => (bool) $publication->is_featured,
            'published_at' => $publication->published_at?->format('Y-m-d'),
            'published_at_label' => $publication->published_at?->format('d/m/Y') ?? 'Não informado',
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
                ->map(fn (PublicationSpeaker $speaker): array => [
                    'id' => $speaker->id,
                    'professional_id' => $speaker->professional_id,
                    'name' => $speaker->displayName(),
                    'guest_name' => $speaker->isGuest() ? $speaker->name : null,
                    'role' => $speaker->professional?->position?->name,
                    'participation' => $speaker->participation,
                    'photo_url' => $speaker->professional?->person?->photo_url,
                ])
                ->values()
                ->all(),
        ];
    }
}
