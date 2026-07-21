<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AccessibilityFeatures;

use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;

final class ShowAccessibilityFeatureQuery
{
    public function execute(AccessibilityFeature $feature): AccessibilityFeature
    {
        return $feature->loadCount('materials');
    }
}
