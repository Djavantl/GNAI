<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\TypeAttributeAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class TypeAttributeAssignmentService
{
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

    public function removeAssignment(int $typeId): void
    {
        DB::transaction(function () use ($typeId) {
            TypeAttributeAssignment::where('type_id', $typeId)->delete();
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
