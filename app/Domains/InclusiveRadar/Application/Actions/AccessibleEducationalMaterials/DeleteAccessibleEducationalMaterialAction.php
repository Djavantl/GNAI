<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Domain\Exceptions\ResourceHasOpenLoans;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteAccessibleEducationalMaterialAction
{
    /**
     * @throws Throwable
     */
    public function execute(AccessibleEducationalMaterial $material): void
    {
        DB::transaction(function () use ($material): void {
            $lockedMaterial = AccessibleEducationalMaterial::query()
                ->lockForUpdate()
                ->findOrFail($material->getKey());

            $hasOpenLoans = $lockedMaterial->loans()
                ->whereNull('return_date')
                ->exists();

            if ($hasOpenLoans) {
                throw new ResourceHasOpenLoans;
            }

            $lockedMaterial->delete();
        });
    }
}
