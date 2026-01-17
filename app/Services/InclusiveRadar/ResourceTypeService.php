<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\ResourceType;

class ResourceTypeService
{
    public function create(array $data): ResourceType
    {
        return ResourceType::create($data);
    }

    public function update(ResourceType $resourceType, array $data): ResourceType
    {
        $resourceType->update($data);
        return $resourceType;
    }

    public function toggleActive(ResourceType $resourceType): ResourceType
    {
        $resourceType->is_active = !$resourceType->is_active;
        $resourceType->save();
        return $resourceType;
    }

    public function delete(ResourceType $resourceType): void
    {
        $resourceType->delete();
    }
}
