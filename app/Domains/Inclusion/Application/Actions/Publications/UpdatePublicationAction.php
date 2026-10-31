<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Publications;

use App\Domains\Inclusion\Application\Data\Publications\UpdatePublicationData;
use App\Domains\Inclusion\Domain\DTOs\Publications\UpdatePublicationDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPublication;
use App\Domains\Inclusion\Domain\Models\Publication;
use Throwable;

final readonly class UpdatePublicationAction
{
    public function __construct(private SyncPublicationRelationsAction $relations) {}

    /**
     * @throws InvalidPublication
     * @throws Throwable
     */
    public function execute(Publication $publication, UpdatePublicationData $data): Publication
    {
        $eventDTO = new UpdatePublicationDTO(
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

        $publication->revise($eventDTO);

        return $this->relations->execute(
            event: $publication,
            persist: fn (Publication $publication) => $publication->save(),
            speakerIds: $data->speakerIds,
            cover: $data->cover,
            removeCover: $data->removeCover,
            newGallery: $data->gallery,
            existingGallery: $data->galleryExisting,
        );
    }
}
