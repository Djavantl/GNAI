<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Actions\Impersonation;

use App\Domains\Auth\Domain\Exceptions\InvalidImpersonation;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Auth\Infrastructure\Session\ImpersonationSession;
use Illuminate\Support\Facades\Auth;

final readonly class StartImpersonationAction
{
    public function __construct(
        private ImpersonationSession $impersonationSession,
    ) {}

    /**
     * @throws InvalidImpersonation
     */
    public function execute(User $impersonator, User $target): User
    {
        if (! $impersonator->isAdmin()) {
            throw InvalidImpersonation::impersonatorMustBeAdmin();
        }

        if ($target->isAdmin()) {
            throw InvalidImpersonation::cannotImpersonateAdmin();
        }

        if ($target->is($impersonator)) {
            throw InvalidImpersonation::cannotImpersonateSelf();
        }

        $this->impersonationSession->start((int) $impersonator->getKey());

        Auth::login($target);

        return $target;
    }
}
