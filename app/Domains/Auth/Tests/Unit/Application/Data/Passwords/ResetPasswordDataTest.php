<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Application\Data\Passwords;

use App\Domains\Auth\Application\Data\Passwords\ResetPasswordData;
use Tests\TestCase;

final class ResetPasswordDataTest extends TestCase
{
    public function test_it_maps_snake_case_payload_to_reset_password_data(): void
    {
        $data = ResetPasswordData::from([
            'token' => 'token',
            'email' => 'user@example.com',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        self::assertSame('token', $data->token);
        self::assertSame('user@example.com', $data->email);
        self::assertSame('NewPassword123', $data->password);
        self::assertSame('NewPassword123', $data->passwordConfirmation);
    }
}
