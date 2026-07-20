<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Waitlists;

use App\Domains\InclusiveRadar\Domain\Models\Waitlist;

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
