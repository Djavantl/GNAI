<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Domain\Models;

use App\Domains\Auth\Domain\DTOs\Users\ResetUserPasswordDTO;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class UserTest extends TestCase
{
    public function test_it_applies_password_reset_state(): void
    {
        $user = new User([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'old-password',
        ]);
        $user->remember_token = 'old-token';

        $user->resetPassword(new ResetUserPasswordDTO(
            password: 'NewPassword123',
            rememberToken: 'new-token',
        ));

        self::assertTrue(Hash::check('NewPassword123', $user->password));
        self::assertSame('new-token', $user->remember_token);
    }

    public function test_it_detects_if_user_can_access_system(): void
    {
        self::assertTrue((new User(['is_admin' => true]))->canAccessSystem());
        self::assertTrue((new User(['professional_id' => 10]))->canAccessSystem());
        self::assertTrue((new User(['teacher_id' => 20]))->canAccessSystem());
        self::assertFalse((new User)->canAccessSystem());
    }
}
