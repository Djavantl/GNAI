<?php

declare(strict_types=1);

namespace App\Domains\Auth\Infrastructure\Session;

use Illuminate\Contracts\Session\Session;

final readonly class ImpersonationSession
{
    private const IMPERSONATOR_ID_KEY = 'impersonator_id';

    public function __construct(
        private Session $session,
    ) {}

    public function active(): bool
    {
        return $this->session->has(self::IMPERSONATOR_ID_KEY);
    }

    public function start(int $impersonatorId): void
    {
        $this->session->put(self::IMPERSONATOR_ID_KEY, $impersonatorId);
    }

    public function impersonatorId(): ?int
    {
        $impersonatorId = $this->session->get(self::IMPERSONATOR_ID_KEY);

        return $impersonatorId === null ? null : (int) $impersonatorId;
    }

    public function clear(): void
    {
        $this->session->forget(self::IMPERSONATOR_ID_KEY);
    }
}
