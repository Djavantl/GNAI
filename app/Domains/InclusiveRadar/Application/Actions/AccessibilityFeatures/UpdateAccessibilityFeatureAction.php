<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\AccessibilityFeatures;

use App\Domains\InclusiveRadar\Application\Data\AccessibilityFeatures\UpdateAccessibilityFeatureData;
use App\Domains\InclusiveRadar\Domain\DTOs\AccessibilityFeatures\UpdateAccessibilityFeatureDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidAccessibilityFeature;
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;

final readonly class UpdateAccessibilityFeatureAction
{
    /**
     * @throws InvalidAccessibilityFeature
     */
    public function execute(AccessibilityFeature $feature, UpdateAccessibilityFeatureData $data): AccessibilityFeature
    {
        $featureDTO = new UpdateAccessibilityFeatureDTO(
            name: $data->name,
            description: $data->description,
            isActive: $data->isActive,
        );

        $feature->revise($featureDTO);
        $feature->save();

        return $feature;
    }
}
