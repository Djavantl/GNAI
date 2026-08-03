<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Positions;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Position;
use App\Models\Permission;
use Illuminate\Support\Collection;

final class PositionFormQuery
{
    /**
     * @return array{permissions: Collection<string, Collection<int, Permission>>}
     */
    public function forCreation(): array
    {
        return [
            'permissions' => $this->groupedPermissions(),
        ];
    }

    /**
     * @return array{position: Position, permissions: Collection<string, Collection<int, Permission>>}
     */
    public function forUpdate(Position $position): array
    {
        $position->loadMissing('permissions');

        return [
            'position' => $position,
            'permissions' => $this->groupedPermissions(),
        ];
    }

    /**
     * @return Collection<string, Collection<int, Permission>>
     */
    private function groupedPermissions(): Collection
    {
        return Permission::query()
            ->orderBy('slug')
            ->get()
            ->groupBy(function (Permission $permission): string {
                $prefix = explode('.', $permission->slug)[0];
                $translation = __("permissions.entities.{$prefix}");

                return $translation !== "permissions.entities.{$prefix}"
                    ? $translation
                    : ucfirst($prefix);
            })
            ->sortKeys();
    }
}
