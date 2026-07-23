<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\InstitutionalEvents;

use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteInstitutionalEventAction
{
    /**
     * @throws Throwable
     */
    public function execute(InstitutionalEvent $event): void
    {
        DB::transaction(function () use ($event): void {
            InstitutionalEvent::query()
                ->lockForUpdate()
                ->findOrFail($event->getKey())
                ->delete();
        });
    }
}
