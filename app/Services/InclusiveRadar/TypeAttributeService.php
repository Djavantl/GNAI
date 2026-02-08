<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\TypeAttribute;
use Illuminate\Support\Facades\DB;

class TypeAttributeService
{
    public function store(array $data): TypeAttribute
    {
        return DB::transaction(function () use ($data) {
            return TypeAttribute::create($data);
        });
    }

    public function update(TypeAttribute $attribute, array $data): TypeAttribute
    {
        return DB::transaction(function () use ($attribute, $data) {
            $attribute->update($data);
            return $attribute;
        });
    }

    public function toggleActive(TypeAttribute $attribute): TypeAttribute
    {
        return DB::transaction(function () use ($attribute) {
            $attribute->update(['is_active' => !$attribute->is_active]);
            return $attribute;
        });
    }

    public function delete(TypeAttribute $attribute): void
    {
        DB::transaction(fn() => $attribute->delete());
    }
}
