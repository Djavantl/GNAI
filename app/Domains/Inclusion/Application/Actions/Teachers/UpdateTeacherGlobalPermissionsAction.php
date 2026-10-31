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
            TeacherGlobalPermission::syncPermissions($data->permissions);
        });

        $this->permissionCache->invalidate();
    }
}
