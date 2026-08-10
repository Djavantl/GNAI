<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Waitlists;

use App\Domains\InclusiveRadar\Application\Data\Waitlists\UpdateWaitlistData;
use App\Domains\InclusiveRadar\Domain\DTOs\Waitlists\UpdateWaitlistDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidWaitlist;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;

final class UpdateWaitlistAction
{
    /**
     * @throws InvalidWaitlist
     */
    public function execute(Waitlist $waitlist, UpdateWaitlistData $data): Waitlist
    {
        $waitlistDTO = new UpdateWaitlistDTO(
            status: $data->status,
            observation: $data->observation,
        );

        $waitlist->revise($waitlistDTO);
        $waitlist->save();

        return $waitlist->fresh([
            'waitlistable',
            'student.person',
            'professional.person',
            'user',
        ]);
    }
}
