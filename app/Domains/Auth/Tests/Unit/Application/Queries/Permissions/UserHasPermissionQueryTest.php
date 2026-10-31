<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Application\Queries\Permissions;

use App\Domains\Auth\Application\Queries\Permissions\UserHasPermissionQuery;
use App\Domains\Auth\Domain\Models\User;
use Tests\TestCase;

final class UserHasPermissionQueryTest extends TestCase
{
    public function test_it_grants_any_permission_to_admins(): void
    {
        $admin = new User(['is_admin' => true]);
        $superAdmin = (new User)->forceFill(['is_super_admin' => true]);

        $query = app(UserHasPermissionQuery::class);

        self::assertTrue($query->execute($admin, 'any.permission'));
        self::assertTrue($query->execute($superAdmin, 'any.permission'));
    }
}
