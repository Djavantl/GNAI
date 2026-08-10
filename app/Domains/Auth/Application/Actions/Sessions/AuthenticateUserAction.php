<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Actions\Sessions;

use App\Domains\Auth\Application\Data\Sessions\LoginData;
use App\Domains\Auth\Domain\DTOs\Sessions\LoginCredentialsDTO;
use App\Domains\Auth\Domain\Exceptions\InactiveProfessionalAccount;
use App\Domains\Auth\Domain\Exceptions\InvalidCredentials;
use App\Domains\Auth\Domain\Exceptions\UserWithoutAccess;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Support\Facades\Auth;

final readonly class AuthenticateUserAction
{
    /**
     * @throws InactiveProfessionalAccount
     * @throws InvalidCredentials
     * @throws UserWithoutAccess
     */
    public function execute(LoginData $data): User
    {
        $credentialsDTO = new LoginCredentialsDTO(
            email: $data->email,
            password: $data->password,
        );

        if (! Auth::attempt($credentialsDTO->toAuthCredentials())) {
            throw InvalidCredentials::make();
        }

        $user = Auth::user();

        if ($user->professional?->status === 'inactive') {
            Auth::logout();

            throw InactiveProfessionalAccount::make();
        }

        if (! $user->canAccessSystem()) {
            Auth::logout();

            throw UserWithoutAccess::make();
        }

        return $user;
    }
}
