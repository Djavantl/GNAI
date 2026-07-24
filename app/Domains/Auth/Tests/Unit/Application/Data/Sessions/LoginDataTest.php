<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Application\Data\Sessions;

use App\Domains\Auth\Application\Data\Sessions\LoginData;
use Tests\TestCase;

final class LoginDataTest extends TestCase
{
    public function test_it_maps_login_payload_and_exposes_credentials(): void
    {
        $data = LoginData::from([
            'email' => 'user@example.com',
            'password' => 'secret',
        ]);

        self::assertSame('user@example.com', $data->email);
        self::assertSame('secret', $data->password);
    }
}
