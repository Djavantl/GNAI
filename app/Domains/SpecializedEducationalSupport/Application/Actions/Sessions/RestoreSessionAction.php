<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Sessions;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;

final readonly class RestoreSessionAction
{
    public function execute(Session $session): Session
    {
        $session->restore();

        return $session;
    }
}
