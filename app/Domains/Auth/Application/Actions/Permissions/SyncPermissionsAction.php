<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Actions\Permissions;

use App\Domains\Auth\Application\Permissions\PermissionCache;
use App\Domains\Auth\Application\Permissions\PermissionRegistry;
use App\Domains\Auth\Domain\DTOs\Permissions\PermissionDTO;
use App\Domains\Auth\Domain\Models\Permission;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final readonly class SyncPermissionsAction
{
    public function __construct(
        private PermissionRegistry $registry,
        private PermissionCache $permissionCache,
    ) {}

    /**
     * @return array{created: int, updated: int, deleted: int, total: int}
     *
     * @throws Throwable
     */
    public function execute(bool $prune = false): array
    {
        $permissions = $this->registry->discover();
        $created = 0;
        $updated = 0;
        $deleted = 0;

        if ($prune && $permissions->isEmpty()) {
            throw new RuntimeException('Nenhuma permissão foi descoberta no código. Prune abortado para evitar remoção acidental.');
        }

        DB::transaction(function () use ($permissions, $prune, &$created, &$updated, &$deleted): void {
            $permissionsBySlug = $permissions
                ->keyBy(fn (PermissionDTO $permission): string => $permission->slug);

            $existingPermissions = Permission::query()
                ->whereIn('slug', $permissionsBySlug->keys()->all())
                ->get()
                ->keyBy('slug');

            foreach ($permissionsBySlug as $slug => $data) {
                $permission = $existingPermissions->get($slug);

                if ($permission === null) {
                    Permission::register($data)->save();
                    $created++;

                    continue;
                }

                if ($permission->synchronize($data)) {
                    $permission->save();
                    $updated++;
                }
            }

            if ($prune) {
                $validSlugs = $permissions
                    ->map(fn (PermissionDTO $permission): string => $permission->slug)
                    ->all();

                $deleted = Permission::query()
                    ->whereNotIn('slug', $validSlugs)
                    ->delete();
            }
        });

        $this->registry->writeManifest($permissions);
        $this->permissionCache->invalidate();

        return [
            'created' => $created,
            'updated' => $updated,
            'deleted' => $deleted,
            'total' => $permissions->count(),
        ];
    }
}
