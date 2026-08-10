<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Waitlists;

use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidWaitlist;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;

final class DeleteWaitlistAction
{
    /**
     * @throws InvalidWaitlist
     */
    public function execute(Waitlist $waitlist): void
    {
        $waitlist->ensureCanBeDeleted();
        $waitlist->delete();
    }
}
