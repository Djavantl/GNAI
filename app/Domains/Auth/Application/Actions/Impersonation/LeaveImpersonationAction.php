<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Actions\Impersonation;

use App\Domains\Auth\Application\Queries\Users\FindUserByIdQuery;
use App\Domains\Auth\Domain\Exceptions\InvalidImpersonation;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Auth\Infrastructure\Session\ImpersonationSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final readonly class LeaveImpersonationAction
{
    public function __construct(
        private ImpersonationSession $impersonationSession,
        private FindUserByIdQuery $users,
    ) {}

    /**
     * @throws InvalidImpersonation
     */
    public function execute(): User
    {
        $impersonatorId = $this->impersonationSession->impersonatorId();

        if ($impersonatorId === null) {
            throw InvalidImpersonation::notActive();
        }

        $impersonator = $this->users->execute($impersonatorId);

        if (! $impersonator instanceof User || ! $impersonator->isSuperAdmin()) {
            $this->impersonationSession->clear();

            throw InvalidImpersonation::invalidImpersonator();
        }

        $this->impersonationSession->clear();

        Auth::login($impersonator);

        Log::notice('Personificação encerrada.', [
            'impersonator_id' => $impersonator->getKey(),
        ]);

        return $impersonator;
    }
}
