<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Teachers;

use App\Domains\Auth\Application\Permissions\PermissionCache;
use App\Domains\SpecializedEducationalSupport\Application\Data\Teachers\UpdateTeacherGlobalPermissionsData;
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
            DB::table('teacher_global_permissions')->delete();

            $permissionIds = array_values(array_unique(array_map('intval', $data->permissions)));

            if ($permissionIds !== []) {
                DB::table('teacher_global_permissions')->insert(
                    collect($permissionIds)
                        ->map(fn (int $id): array => [
                            'permission_id' => $id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ])
                        ->all(),
                );
            }
        });

        $this->permissionCache->invalidate();
    }
}
