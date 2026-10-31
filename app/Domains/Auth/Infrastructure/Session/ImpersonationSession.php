<?php

declare(strict_types=1);

namespace App\Domains\Auth\Infrastructure\Session;

use App\Domains\Auth\Domain\Enums\ImpersonationMode;
use Illuminate\Contracts\Session\Session;

final readonly class ImpersonationSession
{
    private const IMPERSONATOR_ID_KEY = 'impersonator_id';

    private const MODE_KEY = 'impersonation_mode';

    public function __construct(
        private Session $session,
    ) {}

    public function active(): bool
    {
        return $this->session->has(self::IMPERSONATOR_ID_KEY);
    }

    public function start(int $impersonatorId, ImpersonationMode $mode): void
    {
        $this->session->put(self::IMPERSONATOR_ID_KEY, $impersonatorId);
        $this->session->put(self::MODE_KEY, $mode->value);
    }

    public function impersonatorId(): ?int
    {
        $impersonatorId = $this->session->get(self::IMPERSONATOR_ID_KEY);

        return $impersonatorId === null ? null : (int) $impersonatorId;
    }

    /**
     * Sessões sem modo registrado são tratadas como somente leitura, a opção mais segura.
     */
    public function mode(): ?ImpersonationMode
    {
        if (! $this->active()) {
            return null;
        }

        return ImpersonationMode::tryFrom((string) $this->session->get(self::MODE_KEY))
            ?? ImpersonationMode::READ_ONLY;
    }

    public function readOnly(): bool
    {
        return $this->mode() === ImpersonationMode::READ_ONLY;
    }

    public function clear(): void
    {
        $this->session->forget([self::IMPERSONATOR_ID_KEY, self::MODE_KEY]);
    }
}
