<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Teachers;

use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class TeacherPermissionsFormQuery
{
    /**
     * @return array{permissions: Collection, globalPermissionsIds: list<int>}
     */
    public function execute(): array
    {
        $permissions = Permission::query()
            ->get()
            ->groupBy(function (Permission $permission): string {
                $prefix = explode('.', $permission->slug)[0];
                $translationKey = "permissions.entities.{$prefix}";
                $translated = __($translationKey);

                return $translated === $translationKey
                    ? ucfirst(str_replace('-', ' ', $prefix))
                    : $translated;
            });

        return [
            'permissions' => $permissions,
            'globalPermissionsIds' => DB::table('teacher_global_permissions')
                ->pluck('permission_id')
                ->map(fn (mixed $id): int => (int) $id)
                ->all(),
        ];
    }
}
