<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Actions\Passwords;

use App\Domains\Auth\Application\Data\Passwords\ResetPasswordData;
use App\Domains\Auth\Domain\DTOs\Passwords\ResetPasswordDTO;
use App\Domains\Auth\Domain\DTOs\Users\ResetUserPasswordDTO;
use App\Domains\Auth\Domain\Exceptions\InvalidPasswordReset;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

final readonly class ResetPasswordAction
{
    public function execute(ResetPasswordData $data): User
    {
        $dataDTO = new ResetPasswordDTO(
            token: $data->token,
            email: $data->email,
            password: $data->password,
            passwordConfirmation: $data->passwordConfirmation,
        );

        $resetUser = null;

        $status = Password::reset(
            $dataDTO->toBrokerCredentials(),
            function (User $user, string $password) use (&$resetUser): void {
                $user->resetPassword(new ResetUserPasswordDTO(
                    password: $password,
                    rememberToken: Str::random(60),
                ));

                $user->save();

                event(new PasswordReset($user));

                $resetUser = $user;
            },
        );

        if ($status !== Password::PASSWORD_RESET || ! $resetUser instanceof User) {
            throw InvalidPasswordReset::invalidToken();
        }

        return $resetUser;
    }
}
