<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\InstitutionalEvents;

use App\Domains\Inclusion\Application\Data\InstitutionalEvents\UpdateInstitutionalEventData;
use App\Domains\Inclusion\Domain\DTOs\InstitutionalEvents\UpdateInstitutionalEventDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidInstitutionalEvent;
use App\Domains\Inclusion\Domain\Models\InstitutionalEvent;
use Throwable;

final readonly class UpdateInstitutionalEventAction
{
    public function __construct(private SyncPublicationRelationsAction $relations) {}

    /**
     * @throws InvalidInstitutionalEvent
     * @throws Throwable
     */
    public function execute(InstitutionalEvent $event, UpdateInstitutionalEventData $data): InstitutionalEvent
    {
        $eventDTO = new UpdateInstitutionalEventDTO(
            title: $data->title,
            startDate: $data->startDate,
            endDate: $data->endDate,
            startTime: $data->startTime,
            endTime: $data->endTime,
            location: $data->location,
            description: $data->description,
            organizer: $data->organizer,
            audience: $data->audience,
            isActive: $data->isActive,
            hasSchedule: $data->hasSchedule,
            type: $data->type,
            summary: $data->summary,
            category: $data->category,
            slug: $data->slug,
            authorId: $data->authorId,
            linkUrl: $data->linkUrl,
            linkLabel: $data->linkLabel,
            coverAlt: $data->coverAlt,
            publishedAt: $data->publishedAt,
            isFeatured: $data->isFeatured,
            seoTitle: $data->seoTitle,
            seoDescription: $data->seoDescription,
        );

        $event->revise($eventDTO);

        return $this->relations->execute(
            event: $event,
            persist: fn (InstitutionalEvent $event) => $event->save(),
            speakerIds: $data->speakerIds,
            cover: $data->cover,
            removeCover: $data->removeCover,
            newGallery: $data->gallery,
            existingGallery: $data->galleryExisting,
        );
    }
}
