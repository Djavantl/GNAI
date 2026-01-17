<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\TypeAttribute;

class TypeAttributeService
{
    public function create(array $data): TypeAttribute
    {
        return TypeAttribute::create($data);
    }

    public function update(TypeAttribute $attribute, array $data): TypeAttribute
    {
        $attribute->update($data);
        return $attribute;
    }

    public function toggleActive(TypeAttribute $attribute): TypeAttribute
    {
        $attribute->is_active = !$attribute->is_active;
        $attribute->save();
        return $attribute;
    }

    public function delete(TypeAttribute $attribute): void
    {
        $attribute->delete();
    }
}
