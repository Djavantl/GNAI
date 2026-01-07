<?php

namespace App\Services;

use App\Models\AccessibleEducationalMaterial;
use Illuminate\Support\Facades\DB;

class AccessibleEducationalMaterialService
{
    public function listAll()
    {
        return AccessibleEducationalMaterial::with('status', 'deficiencies')
            ->orderBy('title')
            ->get();
    }

    public function store(array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($data) {
            $material = AccessibleEducationalMaterial::create($data);

            if (isset($data['deficiencies'])) {
                $material->deficiencies()->sync($data['deficiencies']);
            }

            return $material;
        });
    }

    public function update(
        AccessibleEducationalMaterial $material,
        array $data
    ): AccessibleEducationalMaterial {
        return DB::transaction(function () use ($material, $data) {
            $material->update($data);

            if (isset($data['deficiencies'])) {
                $material->deficiencies()->sync($data['deficiencies']);
            }

            return $material;
        });
    }

    public function toggleActive(
        AccessibleEducationalMaterial $material
    ): AccessibleEducationalMaterial {
        return DB::transaction(function () use ($material) {
            $material->update([
                'is_active' => ! $material->is_active
            ]);

            return $material;
        });
    }

    public function delete(
        AccessibleEducationalMaterial $material
    ): void {
        DB::transaction(function () use ($material) {
            $material->delete();
        });
    }
}
