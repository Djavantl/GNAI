<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use Illuminate\Support\Facades\DB;

class AccessibleEducationalMaterialService
{

    public function __construct(
        protected AccessibleEducationalMaterialImageService $imageService
    ) {}

    public function listAll()
    {
        return AccessibleEducationalMaterial::with('status')
            ->latest()
            ->paginate(10);
    }

    public function store(array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($data) {
            $material = AccessibleEducationalMaterial::create($data);

            if (isset($data['deficiencies'])) {
                $material->deficiencies()->sync($data['deficiencies']);
            }

            if (isset($data['accessibility_features'])) {
                $material->accessibilityFeatures()->sync($data['accessibility_features']);
            }

            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $imageFile) {
                    $this->imageService->store($material, $imageFile);
                }
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

            if (isset($data['accessibility_features'])) {
                $material->accessibilityFeatures()->sync($data['accessibility_features']);
            }

            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $imageFile) {
                    $this->imageService->store($material, $imageFile);
                }
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
