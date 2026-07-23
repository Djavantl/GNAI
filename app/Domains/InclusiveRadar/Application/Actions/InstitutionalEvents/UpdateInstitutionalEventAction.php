<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\InstitutionalEvents;

use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\UpdateInstitutionalEventData;
use App\Domains\InclusiveRadar\Domain\DTOs\InstitutionalEvents\UpdateInstitutionalEventDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitutionalEvent;
use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent;

final readonly class UpdateInstitutionalEventAction
{
    /**
     * @throws InvalidInstitutionalEvent
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
        );

        $event->revise($eventDTO);
        $event->save();

        return $event;
    }
}
