<?php

declare(strict_types=1);

namespace App\Domains\Auth\Infrastructure\Session;

use Illuminate\Contracts\Session\Session;

final readonly class ImpersonationState
{
    public function __construct(
        private Session $session,
    ) {}

    public function active(): bool
    {
        return $this->session->has('impersonator_id');
    }
}
