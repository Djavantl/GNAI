<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AccessibleEducationalMaterialService
{
    public function __construct(
        protected AccessibleEducationalMaterialImageService $imageService
    ) {}

    public function listAll(): LengthAwarePaginator
    {
        return AccessibleEducationalMaterial::with([
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures',
            'images',
        ])
            ->latest()
            ->paginate(10);
    }

    public function store(array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($data) {
            $material = AccessibleEducationalMaterial::create($data);

            if (!empty($data['deficiencies'])) {
                $material->deficiencies()->sync($data['deficiencies']);
            }

            if (!empty($data['accessibility_features'])) {
                $material->accessibilityFeatures()->sync($data['accessibility_features']);
            }

            if (!empty($data['images'])) {
                foreach ($data['images'] as $imageFile) {
                    $this->imageService->store($material, $imageFile);
                }
            }

            return $material;
        });
    }

    public function update(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($material, $data) {
            $material->update($data);

            if (array_key_exists('deficiencies', $data)) {
                $material->deficiencies()->sync($data['deficiencies'] ?? []);
            }

            if (array_key_exists('accessibility_features', $data)) {
                $material->accessibilityFeatures()->sync($data['accessibility_features'] ?? []);
            }

            if (!empty($data['images'])) {
                foreach ($data['images'] as $imageFile) {
                    $this->imageService->store($material, $imageFile);
                }
            }

            return $material;
        });
    }

    public function toggleActive(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($material) {
            $material->update([
                'is_active' => ! $material->is_active,
            ]);

            return $material;
        });
    }

    public function getCreateData(): array
    {
        return [
            'deficiencies' => Deficiency::orderBy('name')->get(),
        ];
    }

    public function getEditData(AccessibleEducationalMaterial $material): array
    {
        $material->load([
            'deficiencies',
            'accessibilityFeatures',
            'images',
        ]);

        return [
            'material' => $material,
            'attributeValues' => [],
        ];
    }

    public function delete(AccessibleEducationalMaterial $material): void
    {
        DB::transaction(function () use ($material) {
            $material->delete();
        });
    }
}
