<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Actions\Passwords;

use App\Domains\Auth\Application\Data\Passwords\SendPasswordResetLinkData;
use App\Domains\Auth\Domain\DTOs\Passwords\PasswordBrokerResultDTO;
use App\Domains\Auth\Domain\DTOs\Passwords\SendPasswordResetLinkDTO;
use Illuminate\Support\Facades\Password;

final readonly class SendPasswordResetLinkAction
{
    public function execute(SendPasswordResetLinkData $data): PasswordBrokerResultDTO
    {
        $dataDTO = new SendPasswordResetLinkDTO(
            email: $data->email,
        );

        return new PasswordBrokerResultDTO(
            Password::sendResetLink($dataDTO->toBrokerCredentials()),
        );
    }
}
