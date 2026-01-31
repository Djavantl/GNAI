<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\{AccessibleEducationalMaterial, ResourceType, ResourceStatus};
use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccessibleEducationalMaterialService
{
    public function __construct(
        protected AccessibleEducationalMaterialImageService $imageService,
        protected ResourceAttributeValueService $attributeValueService
    ) {}

    public function listAll(): Collection
    {
        return AccessibleEducationalMaterial::with([
            'type',
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures',
            'images',
        ])
            ->orderBy('title')
            ->get();
    }

    public function getCreateData(): array
    {
        return [
            'deficiencies' => Deficiency::where('is_active', true)->orderBy('name')->get(),
        ];
    }

    public function getEditData(AccessibleEducationalMaterial $material): array
    {
        return [
            'material' => $material->load(['deficiencies', 'accessibilityFeatures', 'images']),
            'attributeValues' => [],
        ];
    }

    public function store(array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($data) {
            $type = ResourceType::find($data['type_id']);
            $data['quantity_available'] = ($type && $type->is_digital) ? null : ($data['quantity'] ?? 0);
            if ($type && $type->is_digital) $data['quantity'] = null;

            $material = AccessibleEducationalMaterial::create($data);

            if (!empty($data['deficiencies'])) $material->deficiencies()->sync($data['deficiencies']);

            if (!empty($data['accessibility_features'])) {
                $material->accessibilityFeatures()->sync($data['accessibility_features']);
            }

            $this->attributeValueService->saveValues(
                'accessible_educational_material',
                $material->id,
                $data['attributes'] ?? []
            );

            if (!empty($data['images'])) {
                foreach ($data['images'] as $img) $this->imageService->store($material, $img);
            }

            return $material;
        });
    }

    public function update(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($material, $data) {
            $type = ResourceType::find($data['type_id'] ?? $material->type_id);

            if ($type && $type->is_digital) {
                $data['quantity'] = $data['quantity_available'] = null;
            } else {
                $newTotal = (int) ($data['quantity'] ?? 0);
                $activeLoans = $material->loans()->whereNull('return_date')->count();

                if ($newTotal < $activeLoans) {
                    throw ValidationException::withMessages([
                        'quantity' => "Mínimo permitido: {$activeLoans} (empréstimos pendentes)."
                    ]);
                }

                $data['quantity_available'] = $newTotal - $activeLoans;

                if ($data['quantity_available'] > 0) {
                    $status = ResourceStatus::where('code', 'available')->first();
                    if ($status) $data['status_id'] = $status->id;
                }
            }

            $material->update($data);

            if (array_key_exists('deficiencies', $data)) {
                $material->deficiencies()->sync($data['deficiencies'] ?? []);
            }

            if (array_key_exists('accessibility_features', $data)) {
                $material->accessibilityFeatures()->sync($data['accessibility_features'] ?? []);
            }

            $this->attributeValueService->saveValues(
                'accessible_educational_material',
                $material->id,
                $data['attributes'] ?? []
            );

            if (!empty($data['images'])) {
                foreach ($data['images'] as $img) $this->imageService->store($material, $img);
            }

            return $material->fresh();
        });
    }

    public function toggleActive(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($material) {
            $material->update(['is_active' => !$material->is_active]);
            return $material;
        });
    }

    public function delete(AccessibleEducationalMaterial $material): void
    {
        DB::transaction(function () use ($material) {
            $hasOpenLoans = $material->loans()
                ->whereNull('return_date')
                ->exists();

            if ($hasOpenLoans) {
                throw ValidationException::withMessages([
                    'delete' => 'Não é possível excluir: este material ainda não foi devolvido.'
                ]);
            }

            $material->delete();
        });
    }

    public function getItemHistory(AccessibleEducationalMaterial $material): Collection
    {
        return $material->loans()
            ->with(['student.person', 'professional.person'])
            ->orderByDesc('loan_date')
            ->get();
    }
}
