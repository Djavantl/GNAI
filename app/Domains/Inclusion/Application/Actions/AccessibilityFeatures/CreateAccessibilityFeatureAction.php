<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\AccessibilityFeatures;

use App\Domains\Inclusion\Application\Data\AccessibilityFeatures\CreateAccessibilityFeatureData;
use App\Domains\Inclusion\Domain\DTOs\AccessibilityFeatures\CreateAccessibilityFeatureDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidAccessibilityFeature;
use App\Domains\Inclusion\Domain\Models\AccessibilityFeature;

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
            isActive: $data->isActive,
        );

        $feature = AccessibilityFeature::register($featureDTO);
        $feature->save();

        return $feature;
    }
}
