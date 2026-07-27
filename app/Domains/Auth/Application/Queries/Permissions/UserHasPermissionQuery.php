<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Queries\Permissions;

use App\Domains\Auth\Application\Permissions\PermissionCache;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class UserHasPermissionQuery
{
    /**
     * @var array<string, Collection<int, string>>
     */
    private array $permissionSlugsByCacheKey = [];

    public function __construct(
        private readonly PermissionCache $permissionCache,
    ) {}

    public function execute(User $user, string $permissionSlug): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->permissionSlugsFor($user)->contains($permissionSlug);
    }

    /**
     * @return Collection<int, string>
     */
    private function permissionSlugsFor(User $user): Collection
    {
        $cacheKey = sprintf(
            'auth.permission_slugs.v%d.user.%d',
            $this->permissionCache->version(),
            $user->getKey(),
        );

        if (isset($this->permissionSlugsByCacheKey[$cacheKey])) {
            return $this->permissionSlugsByCacheKey[$cacheKey];
        }

        return $this->permissionSlugsByCacheKey[$cacheKey] = Cache::remember($cacheKey, now()->addHour(), function () use ($user): Collection {
            $slugs = collect();

            if ($user->professional_id) {
                $slugs = $slugs->merge(
                    DB::table('permissions')
                        ->join('permission_position', 'permission_position.permission_id', '=', 'permissions.id')
                        ->join('professionals', 'professionals.position_id', '=', 'permission_position.position_id')
                        ->where('professionals.id', $user->professional_id)
                        ->pluck('permissions.slug')
                );
            }

            if ($user->teacher_id) {
                $slugs = $slugs->merge(
                    DB::table('teacher_global_permissions')
                        ->join('permissions', 'permissions.id', '=', 'teacher_global_permissions.permission_id')
                        ->pluck('permissions.slug')
                );
            }

            return $slugs
                ->unique()
                ->values();
        });
    }
}
