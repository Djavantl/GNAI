<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Waitlists;

use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use Illuminate\Support\Facades\DB;
use Throwable;

final class DeleteWaitlistAction
{
    /**
     * @throws Throwable
     */
    public function execute(Waitlist $waitlist): void
    {
        DB::transaction(function () use ($waitlist): void {
            $waitlist->ensureCanBeDeleted();
            $waitlist->delete();
        });
    }
}
