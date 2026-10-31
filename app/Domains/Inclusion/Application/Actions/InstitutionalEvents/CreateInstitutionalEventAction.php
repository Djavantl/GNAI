<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\InstitutionalEvents;

use App\Domains\Inclusion\Application\Data\InstitutionalEvents\CreateInstitutionalEventData;
use App\Domains\Inclusion\Domain\DTOs\InstitutionalEvents\CreateInstitutionalEventDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidInstitutionalEvent;
use App\Domains\Inclusion\Domain\Models\InstitutionalEvent;
use Throwable;

final readonly class CreateInstitutionalEventAction
{
    public function __construct(private SyncPublicationRelationsAction $relations) {}

    /**
     * @throws InvalidInstitutionalEvent
     * @throws Throwable
     */
    public function execute(CreateInstitutionalEventData $data): InstitutionalEvent
    {
        $eventDTO = new CreateInstitutionalEventDTO(
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

        $event = InstitutionalEvent::schedule($eventDTO);

        return $this->relations->execute(
            event: $event,
            persist: fn (InstitutionalEvent $event) => $event->save(),
            speakerIds: $data->speakerIds,
            cover: $data->cover,
            removeCover: false,
            newGallery: $data->gallery,
            existingGallery: [],
        );
    }
}
