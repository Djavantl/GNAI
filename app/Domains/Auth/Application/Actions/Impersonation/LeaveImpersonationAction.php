<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Actions\Impersonation;

use App\Domains\Auth\Application\Queries\Users\FindUserByIdQuery;
use App\Domains\Auth\Domain\Exceptions\InvalidImpersonation;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Auth\Infrastructure\Session\ImpersonationSession;
use Illuminate\Support\Facades\Auth;

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

        if (! $impersonator instanceof User || ! $impersonator->isAdmin()) {
            $this->impersonationSession->clear();

            throw InvalidImpersonation::invalidImpersonator();
        }

        $this->impersonationSession->clear();

        Auth::login($impersonator);

        return $impersonator;
    }
}
