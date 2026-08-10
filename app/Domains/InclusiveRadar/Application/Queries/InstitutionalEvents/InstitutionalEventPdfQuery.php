<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\InstitutionalEvents;

use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent;

final class InstitutionalEventPdfQuery
{
    public function execute(InstitutionalEvent $event): InstitutionalEvent
    {
        return $event;
    }
}
