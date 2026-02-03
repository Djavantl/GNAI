<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\TypeAttribute;
use App\Models\InclusiveRadar\ResourceType;
use App\Models\InclusiveRadar\TypeAttributeAssignment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TypeAttributeAssignmentService
{
    public function listAll(): Collection
    {
        return TypeAttributeAssignment::with(['type', 'attribute'])
            ->orderBy('id')
            ->get();
    }

    public function getCreateData(): array
    {
        return [
            'types' => ResourceType::where('is_active', true)->orderBy('name')->get(),
            'attributes' => TypeAttribute::where('is_active', true)->orderBy('label')->get(),
        ];
    }

    public function getEditData(ResourceType $type): array
    {
        $assignedAttributeIds = TypeAttributeAssignment::where('type_id', $type->id)
            ->pluck('attribute_id')
            ->toArray();

        return [
            'type' => $type,
            'types' => ResourceType::where('is_active', true)->orderBy('name')->get(),
            'attributes' => TypeAttribute::where('is_active', true)->orderBy('label')->get(),
            'assignedAttributeIds' => $assignedAttributeIds,
        ];
    }

    public function assignAttributesToType(int $typeId, array $attributeIds): void
    {
        DB::transaction(function () use ($typeId, $attributeIds) {
            foreach ($attributeIds as $attributeId) {
                TypeAttributeAssignment::firstOrCreate([
                    'type_id' => $typeId,
                    'attribute_id' => $attributeId,
                ]);
            }
        });
    }

    public function syncAttributes(int $typeId, array $attributeIds): void
    {
        DB::transaction(function () use ($typeId, $attributeIds) {
            TypeAttributeAssignment::where('type_id', $typeId)->delete();

            foreach ($attributeIds as $attributeId) {
                TypeAttributeAssignment::create([
                    'type_id' => $typeId,
                    'attribute_id' => $attributeId,
                ]);
            }
        });
    }

    public function update(TypeAttributeAssignment $assignment, array $data): TypeAttributeAssignment
    {
        return DB::transaction(function () use ($assignment, $data) {
            $assignment->update([
                'type_id' => $data['type_id'],
                'attribute_id' => $data['attribute_id'],
            ]);

            return $assignment;
        });
    }

    public function removeAssignment(ResourceType $type): void
    {
        DB::transaction(function () use ($type) {
            TypeAttributeAssignment::where('type_id', $type->id)->delete();
        });
    }

    public function getAttributesByTypeId(int $typeId): Collection
    {
        return TypeAttributeAssignment::with('attribute')
            ->where('type_id', $typeId)
            ->get()
            ->pluck('attribute');
    }
}
