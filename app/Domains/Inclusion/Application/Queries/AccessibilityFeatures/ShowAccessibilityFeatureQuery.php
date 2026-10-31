<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\AccessibilityFeatures;

use App\Domains\Inclusion\Domain\Models\AccessibilityFeature;

final class ShowAccessibilityFeatureQuery
{
    public function execute(AccessibilityFeature $feature): AccessibilityFeature
    {
        return $feature->loadCount('items');
    }
}
