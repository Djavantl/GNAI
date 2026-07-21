<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\AccessibilityFeatures;

use App\Domains\InclusiveRadar\Application\Data\AccessibilityFeatures\CreateAccessibilityFeatureData;
use App\Domains\InclusiveRadar\Domain\DTOs\AccessibilityFeatures\CreateAccessibilityFeatureDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidAccessibilityFeature;
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;

final readonly class CreateAccessibilityFeatureAction
{
    /**
     * @throws InvalidAccessibilityFeature
     */
    public function execute(CreateAccessibilityFeatureData $data): AccessibilityFeature
    {
        $featureDTO = new CreateAccessibilityFeatureDTO(
            name: $data->name,
            description: $data->description,
            active: $data->isActive,
        );

        $feature = AccessibilityFeature::register($featureDTO);
        $feature->save();

        return $feature;
    }
}
