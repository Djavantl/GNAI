<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Application\Queries\Permissions;

use App\Domains\Auth\Application\Queries\Permissions\UserHasPermissionQuery;
use App\Domains\Auth\Domain\Models\User;
use Tests\TestCase;

final class UserHasPermissionQueryTest extends TestCase
{
    public function test_admin_user_has_any_permission(): void
    {
        $user = new User(['is_admin' => true]);

        self::assertTrue(app(UserHasPermissionQuery::class)->execute($user, 'any.permission'));
    }
}
