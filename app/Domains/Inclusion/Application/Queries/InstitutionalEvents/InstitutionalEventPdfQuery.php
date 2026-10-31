<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\InstitutionalEvents;

use App\Domains\Inclusion\Domain\Models\InstitutionalEvent;

final class InstitutionalEventPdfQuery
{
    public function execute(InstitutionalEvent $event): InstitutionalEvent
    {
        return $event;
    }
}
