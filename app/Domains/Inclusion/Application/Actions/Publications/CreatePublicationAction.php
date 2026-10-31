<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Publications;

use App\Domains\Inclusion\Application\Data\Publications\CreatePublicationData;
use App\Domains\Inclusion\Domain\DTOs\Publications\CreatePublicationDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPublication;
use App\Domains\Inclusion\Domain\Models\Publication;
use Throwable;

final readonly class CreatePublicationAction
{
    public function __construct(private SyncPublicationRelationsAction $relations) {}

    /**
     * @throws InvalidPublication
     * @throws Throwable
     */
    public function execute(CreatePublicationData $data): Publication
    {
        $publicationDTO = new CreatePublicationDTO(
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

        $publication = Publication::schedule($publicationDTO);

        return $this->relations->execute(
            publication: $publication,
            speakers: $data->speakers,
            tagIds: $data->tagIds,
            cover: $data->cover,
            removeCover: false,
            newGallery: $data->gallery,
            existingGallery: [],
        );
    }
}
