<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\ResourceType;
use Illuminate\Support\Facades\DB;

class ResourceTypeService
{
    public function store(array $data): ResourceType
    {
        return DB::transaction(function () use ($data) {
            return ResourceType::create($data);
        });
    }

    public function update(ResourceType $resourceType, array $data): ResourceType
    {
        return DB::transaction(function () use ($resourceType, $data) {
            $resourceType->update($data);
            return $resourceType;
        });
    }

    public function toggleActive(ResourceType $resourceType): ResourceType
    {
        return DB::transaction(function () use ($resourceType) {
            $resourceType->update([
                'is_active' => !$resourceType->is_active,
            ]);
            return $resourceType;
        });
    }

    public function delete(ResourceType $resourceType): void
    {
        DB::transaction(fn() => $resourceType->delete());
    }
}
