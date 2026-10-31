<?php

declare(strict_types=1);

namespace App\Domains\Auth\Infrastructure\Providers;

use App\Domains\Audit\Infrastructure\AuditableEntities;
use App\Domains\Auth\Application\Permissions\PermissionCache;
use App\Domains\Auth\Application\Permissions\PermissionRegistry;
use App\Domains\Auth\Application\Queries\Permissions\UserHasPermissionQuery;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Auth\UI\Console\Commands\CreateAdminUser;
use App\Domains\Auth\UI\Console\Commands\ResetAdminPassword;
use App\Domains\Auth\UI\Console\Commands\SyncPermissions;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

final class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(PermissionCache::class);
        $this->app->scoped(PermissionRegistry::class);
        $this->app->scoped(UserHasPermissionQuery::class);
    }

    public function boot(): void
    {
        $this->commands([
            CreateAdminUser::class,
            ResetAdminPassword::class,
            SyncPermissions::class,
        ]);

        $this->app->make(AuditableEntities::class)->register(User::class);

        Gate::before(function ($user, string $ability): ?bool {
            if ($user->isAdmin()) {
                return true;
            }

            if (! app(PermissionRegistry::class)->has($ability)) {
                return null;
            }

            if (! $this->canUsePermissionsTable()) {
                return false;
            }

            return app(UserHasPermissionQuery::class)->execute($user, $ability);
        });
    }

    private function canUsePermissionsTable(): bool
    {
        try {
            return Schema::hasTable('permissions');
        } catch (Throwable) {
            return false;
        }
    }
}
