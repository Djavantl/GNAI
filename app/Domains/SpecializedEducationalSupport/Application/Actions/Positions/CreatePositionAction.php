<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Positions;

use App\Domains\Auth\Application\Permissions\PermissionCache;
use App\Domains\SpecializedEducationalSupport\Application\Data\Positions\CreatePositionData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Positions\CreatePositionDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Position;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreatePositionAction
{
    public function __construct(
        private PermissionCache $permissionCache,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(CreatePositionData $data): Position
    {
        return DB::transaction(function () use ($data): Position {
            $positionDTO = new CreatePositionDTO(
                name: $data->name,
                description: $data->description,
            );

            $position = Position::register($positionDTO);
            $position->save();
            $position->assignPermissions($data->permissions);
            $this->permissionCache->invalidate();

            return $position->load('permissions');
        });
    }
}
