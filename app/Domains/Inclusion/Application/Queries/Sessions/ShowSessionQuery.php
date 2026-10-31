<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Sessions;

use App\Domains\Inclusion\Domain\Models\Session;

final class ShowSessionQuery
{
    public function execute(Session $session): Session
    {
        return $session->load(['students.person', 'professional.person', 'aeeRecord', 'pedagogicalRecord']);
    }
}
