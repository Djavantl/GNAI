<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\AccessibilityFeatures;

use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteAccessibilityFeatureAction
{
    /**
     * @throws Throwable
     */
    public function execute(AccessibilityFeature $feature): void
    {
        DB::transaction(function () use ($feature): void {
            AccessibilityFeature::query()
                ->lockForUpdate()
                ->findOrFail($feature->getKey())
                ->delete();
        });
    }
}
