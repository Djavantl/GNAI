<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\InstitutionalEvents;

use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\CreateInstitutionalEventData;
use App\Domains\InclusiveRadar\Domain\DTOs\InstitutionalEvents\CreateInstitutionalEventDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitutionalEvent;
use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent;

final readonly class CreateInstitutionalEventAction
{
    /**
     * @throws InvalidInstitutionalEvent
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
        );

        $event = InstitutionalEvent::schedule($eventDTO);
        $event->save();

        return $event;
    }
}
