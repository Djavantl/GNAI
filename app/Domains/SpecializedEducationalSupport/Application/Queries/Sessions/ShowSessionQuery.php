<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;

final class ShowSessionQuery
{
    public function execute(Session $session): Session
    {
        return $session->load(['students.person', 'professional.person', 'sessionRecord', 'pedagogicalRecord']);
    }
}
