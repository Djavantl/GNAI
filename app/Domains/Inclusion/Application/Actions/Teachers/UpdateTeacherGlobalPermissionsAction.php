<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Teachers;

use App\Domains\Auth\Application\Permissions\PermissionCache;
use App\Domains\Inclusion\Application\Data\Teachers\UpdateTeacherGlobalPermissionsData;
use App\Domains\Inclusion\Domain\Models\TeacherGlobalPermission;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateTeacherGlobalPermissionsAction
{
    public function __construct(
        private PermissionCache $permissionCache,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(UpdateTeacherGlobalPermissionsData $data): void
    {
        DB::transaction(function () use ($data): void {
            $permissionIds = array_values(array_unique(array_map('intval', $data->permissions)));

            // Registro a registro (e não delete/insert em massa) para cada concessão e revogação entrar na auditoria.
            TeacherGlobalPermission::query()
                ->whereNotIn('permission_id', $permissionIds)
                ->get()
                ->each(fn (TeacherGlobalPermission $permission) => $permission->delete());

            $current = TeacherGlobalPermission::query()->pluck('permission_id')->map(fn (mixed $id): int => (int) $id)->all();

            foreach (array_diff($permissionIds, $current) as $permissionId) {
                TeacherGlobalPermission::query()->create(['permission_id' => $permissionId]);
            }
        });

        $this->permissionCache->invalidate();
    }
}
