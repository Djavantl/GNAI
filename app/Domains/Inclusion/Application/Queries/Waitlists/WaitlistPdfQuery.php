<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Waitlists;

use App\Domains\Inclusion\Domain\Models\Waitlist;

final class WaitlistPdfQuery
{
    public function execute(Waitlist $waitlist): Waitlist
    {
        return $waitlist->load([
            'waitlistable',
            'student:id,person_id,registration',
            'student.person:id,name',
            'professional:id,person_id,registration',
            'professional.person:id,name',
            'user:id,name',
        ]);
    }
}
