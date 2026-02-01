<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\ResourceAttributeValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class ResourceAttributeValueService
{
    private function getMorphType(Model $resource): string
    {
        return array_search(
            get_class($resource),
            Relation::morphMap(),
            true
        ) ?: get_class($resource);
    }

    public function saveValues(Model $resource, array $attributesValues): void
    {
        $resourceType = $this->getMorphType($resource);

        foreach ($attributesValues as $attributeId => $value) {
            if (is_null($value)) {
                continue;
            }

            ResourceAttributeValue::updateOrCreate(
                [
                    'resource_type' => $resourceType,
                    'resource_id'   => $resource->getKey(),
                    'attribute_id'  => $attributeId,
                ],
                [
                    'value' => $value
                ]
            );
        }
    }

    public function removeValues(Model $resource): void
    {
        ResourceAttributeValue::where('resource_type', $this->getMorphType($resource))
            ->where('resource_id', $resource->getKey())
            ->delete();
    }

    public function getValues(Model $resource): Collection
    {
        return ResourceAttributeValue::with('attribute')
            ->where('resource_type', $this->getMorphType($resource))
            ->where('resource_id', $resource->getKey())
            ->get()
            ->keyBy('attribute_id');
    }

    public function getValuesForForm(Model $resource): array
    {
        return ResourceAttributeValue::where('resource_type', $this->getMorphType($resource))
            ->where('resource_id', $resource->getKey())
            ->pluck('value', 'attribute_id')
            ->toArray();
    }
}
