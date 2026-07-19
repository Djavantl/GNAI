<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use Illuminate\Support\Facades\DB;

final readonly class DeleteAccessibleEducationalMaterialAction
{
    public function execute(AccessibleEducationalMaterial $material): void
    {
        DB::transaction(function () use ($material): void {
            $lockedMaterial = AccessibleEducationalMaterial::query()
                ->lockForUpdate()
                ->findOrFail($material->getKey());

            $hasOpenLoans = $lockedMaterial->loans()
                ->whereNull('return_date')
                ->exists();

            $lockedMaterial->ensureCanBeRemoved($hasOpenLoans);

            $lockedMaterial->delete();
        });
    }
}
