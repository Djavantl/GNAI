<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\TypeAttributeAssignment;
use Illuminate\Support\Facades\DB;

class TypeAttributeAssignmentService
{

    public function assignAttributesToType(int $typeId, array $attributeIds): void
    {
        DB::transaction(function () use ($typeId, $attributeIds) {
            foreach ($attributeIds as $attributeId) {
                TypeAttributeAssignment::firstOrCreate([
                    'type_id' => $typeId,
                    'attribute_id' => $attributeId
                ]);
            }
        });
    }

    public function updateAssignment(TypeAttributeAssignment $assignment, array $data): bool
    {
        return $assignment->update([
            'type_id' => $data['type_id'],
            'attribute_id' => $data['attribute_id']
        ]);
    }

    public function removeAssignment(int $id): void
    {
        TypeAttributeAssignment::destroy($id);
    }

    public function syncAttributes(int $typeId, array $attributeIds): void
    {
        DB::transaction(function () use ($typeId, $attributeIds) {
            TypeAttributeAssignment::where('type_id', $typeId)->delete();

            foreach ($attributeIds as $attributeId) {
                TypeAttributeAssignment::create([
                    'type_id' => $typeId,
                    'attribute_id' => $attributeId
                ]);
            }
        });
    }

    public function getAttributesByTypeId(int $typeId)
    {
        return TypeAttributeAssignment::with('attribute')
            ->where('type_id', $typeId)
            ->get()
            ->pluck('attribute');
    }
}
