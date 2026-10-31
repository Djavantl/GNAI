<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Domain\Models;

use App\Domains\Auth\Domain\Models\User;
use Tests\TestCase;

final class UserTest extends TestCase
{
    public function test_it_detects_if_user_can_access_system(): void
    {
        self::assertTrue((new User(['is_admin' => true]))->canAccessSystem());
        self::assertTrue((new User)->forceFill(['is_super_admin' => true])->canAccessSystem());
        self::assertTrue((new User(['professional_id' => 10]))->canAccessSystem());
        self::assertTrue((new User(['teacher_id' => 20]))->canAccessSystem());
        self::assertFalse((new User)->canAccessSystem());
    }
}
