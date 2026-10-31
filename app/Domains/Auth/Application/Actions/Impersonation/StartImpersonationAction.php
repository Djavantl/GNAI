<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Actions\Impersonation;

use App\Domains\Auth\Domain\Enums\ImpersonationMode;
use App\Domains\Auth\Domain\Exceptions\InvalidImpersonation;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Auth\Infrastructure\Session\ImpersonationSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final readonly class StartImpersonationAction
{
    public function __construct(
        private ImpersonationSession $impersonationSession,
    ) {}

    /**
     * @throws InvalidImpersonation
     */
    public function execute(User $impersonator, User $target, ImpersonationMode $mode): User
    {
        if (! $impersonator->isSuperAdmin()) {
            throw InvalidImpersonation::impersonatorMustBeSuperAdmin();
        }

        if ($target->is($impersonator)) {
            throw InvalidImpersonation::cannotImpersonateSelf();
        }

        if ($target->isSuperAdmin()) {
            throw InvalidImpersonation::cannotImpersonateSuperAdmin();
        }

        if (! $target->canAccessSystem()) {
            throw InvalidImpersonation::targetWithoutAccess();
        }

        $this->impersonationSession->start((int) $impersonator->getKey(), $mode);

        Auth::login($target);

        Log::notice('Personificação iniciada.', [
            'impersonator_id' => $impersonator->getKey(),
            'target_id' => $target->getKey(),
            'mode' => $mode->value,
        ]);

        return $target;
    }
}
