<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Queries\Permissions;

use App\Domains\Auth\Domain\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class UserHasPermissionQuery
{
    public function execute(User $user, string $permissionSlug): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $hasProfessionalPermission = $user->professional
            ?->position
            ?->permissions
            ->contains('slug', $permissionSlug) ?? false;

        if ($hasProfessionalPermission) {
            return true;
        }

        if ($user->teacher_id) {
            return DB::table('teacher_global_permissions')
                ->join('permissions', 'permissions.id', '=', 'teacher_global_permissions.permission_id')
                ->where('permissions.slug', $permissionSlug)
                ->exists();
        }

        return false;
    }
}
