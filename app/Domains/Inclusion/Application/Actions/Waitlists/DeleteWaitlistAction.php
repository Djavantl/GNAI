<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Waitlists;

use App\Domains\Inclusion\Domain\Exceptions\InvalidWaitlist;
use App\Domains\Inclusion\Domain\Models\Waitlist;

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
