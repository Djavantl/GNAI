<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Waitlists;

use App\Domains\Inclusion\Domain\Exceptions\InvalidWaitlist;
use App\Domains\Inclusion\Domain\Models\Waitlist;

final class CancelWaitlistAction
{
    /**
     * @throws InvalidWaitlist
     */
    public function execute(Waitlist $waitlist): Waitlist
    {
        $waitlist->cancel();
        $waitlist->save();

        return $waitlist->fresh([
            'waitlistable',
            'student.person',
            'professional.person',
            'user',
        ]);
    }
}
