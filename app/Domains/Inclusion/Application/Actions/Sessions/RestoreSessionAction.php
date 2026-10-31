<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Sessions;

use App\Domains\Inclusion\Domain\Models\Session;

final readonly class RestoreSessionAction
{
    public function execute(Session $session): Session
    {
        $session->restore();

        return $session;
    }
}
