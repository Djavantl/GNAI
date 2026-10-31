<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Positions;

use App\Domains\Auth\Application\Permissions\PermissionCache;
use App\Domains\Inclusion\Application\Data\Positions\UpdatePositionData;
use App\Domains\Inclusion\Domain\DTOs\Positions\UpdatePositionDTO;
use App\Domains\Inclusion\Domain\Models\Position;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdatePositionAction
{
    public function __construct(
        private PermissionCache $permissionCache,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Position $position, UpdatePositionData $data): Position
    {
        return DB::transaction(function () use ($position, $data): Position {
            $lockedPosition = Position::query()
                ->lockForUpdate()
                ->findOrFail($position->getKey());

            $positionDTO = new UpdatePositionDTO(
                name: $data->name,
                description: $data->description,
            );

            $lockedPosition->revise($positionDTO);
            $lockedPosition->save();
            $lockedPosition->assignPermissions($data->permissions);
            $this->permissionCache->invalidate();

            return $lockedPosition->load('permissions');
        });
    }
}
