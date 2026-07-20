<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Waitlists;

use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CancelWaitlistAction
{
    /**
     * @throws Throwable
     */
    public function execute(Waitlist $waitlist): Waitlist
    {
        $cancelledWaitlist = DB::transaction(function () use ($waitlist): Waitlist {
            $waitlist->cancel();
            $waitlist->save();

            return $waitlist;
        });

        return $cancelledWaitlist->fresh([
            'waitlistable',
            'student.person',
            'professional.person',
            'user',
        ]);
    }
}
