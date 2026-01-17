<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\ResourceAttributeValue;

class ResourceAttributeValueService
{
    public function saveValues(string $resourceType, int $resourceId, array $attributesValues): void
    {
        foreach ($attributesValues as $attributeId => $value) {
            ResourceAttributeValue::updateOrCreate(
                [
                    'resource_type' => $resourceType,
                    'resource_id' => $resourceId,
                    'attribute_id' => $attributeId,
                ],
                [
                    'value' => $value
                ]
            );
        }
    }

    public function removeValues(string $resourceType, int $resourceId): void
    {
        ResourceAttributeValue::where('resource_type', $resourceType)
            ->where('resource_id', $resourceId)
            ->delete();
    }

    public function getValues(string $resourceType, int $resourceId)
    {
        return ResourceAttributeValue::with('attribute')
            ->where('resource_type', $resourceType)
            ->where('resource_id', $resourceId)
            ->get()
            ->keyBy('attribute_id');
    }
}
