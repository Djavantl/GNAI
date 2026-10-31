<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Waitlists;

use App\Domains\Inclusion\Domain\Models\Waitlist;

final class ShowWaitlistQuery
{
    public function execute(Waitlist $waitlist): Waitlist
    {
        return $waitlist->loadMissing([
            'waitlistable',
            'student.person',
            'professional.person',
            'user',
        ]);
    }
}
