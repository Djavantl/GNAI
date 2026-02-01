<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\{
    AccessibleEducationalMaterial,
    ResourceType
};
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Enums\InclusiveRadar\{
    ConservationState,
    InspectionType
};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccessibleEducationalMaterialService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected ResourceAttributeValueService $attributeValueService
    ) {}

    public function listAll(): Collection
    {
        return AccessibleEducationalMaterial::with([
            'type',
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures',
        ])
            ->orderBy('name')
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
            'material' => $material->load([
                'deficiencies',
                'accessibilityFeatures',
                'inspections.images'
            ]),
            'attributeValues' => $this->attributeValueService->getValuesForForm($material),
        ];
    }

    public function store(array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($data) {
            $type = ResourceType::find($data['type_id']);

            $data['quantity_available'] = ($type && $type->is_digital)
                ? null
                : ($data['quantity'] ?? 0);

            if ($type && $type->is_digital) {
                $data['quantity'] = null;
            }

            $material = AccessibleEducationalMaterial::create($data);

            if (!empty($data['deficiencies'])) {
                $material->deficiencies()->sync($data['deficiencies']);
            }

            if (!empty($data['accessibility_features'])) {
                $material->accessibilityFeatures()->sync($data['accessibility_features']);
            }

            $this->attributeValueService->saveValues(
                $material,
                $data['attributes'] ?? []
            );

            $this->inspectionService->createForModel($material, [
                'state'           => $data['conservation_state'] ?? ConservationState::NEW->value,
                'inspection_date' => now(),
                'type'            => InspectionType::INITIAL->value,
                'description'     => $data['inspection_description']
                    ?? 'Vistoria inicial realizada no cadastro.',
                'images'          => $data['images'] ?? []
            ]);

            return $material;
        });
    }

    public function update(
        AccessibleEducationalMaterial $material,
        array $data
    ): AccessibleEducationalMaterial {
        return DB::transaction(function () use ($material, $data) {
            $type = ResourceType::find($data['type_id'] ?? $material->type_id);

            if ($type && $type->is_digital) {
                $data['quantity'] = $data['quantity_available'] = null;
            } else {
                $newTotal = (int) ($data['quantity'] ?? 0);
                $activeLoans = $material->loans()
                    ->whereIn('status', ['active', 'late'])
                    ->count();

                if ($newTotal < $activeLoans) {
                    throw ValidationException::withMessages([
                        'quantity' => "Mínimo permitido: {$activeLoans}."
                    ]);
                }

                $data['quantity_available'] = $newTotal - $activeLoans;
            }

            $oldState = $material->getOriginal('conservation_state');
            $newState = $data['conservation_state'] ?? $oldState;

            $material->update($data);

            if (array_key_exists('deficiencies', $data)) {
                $material->deficiencies()->sync($data['deficiencies'] ?? []);
            }

            if (array_key_exists('accessibility_features', $data)) {
                $material->accessibilityFeatures()
                    ->sync($data['accessibility_features'] ?? []);
            }

            $this->attributeValueService->saveValues(
                $material,
                $data['attributes'] ?? []
            );

            $hasNewImages = !empty($data['images']);
            $stateChanged = $newState !== $oldState;

            if ($hasNewImages || $stateChanged) {
                $this->inspectionService->createForModel($material, [
                    'state'           => $newState,
                    'inspection_date' => $data['inspection_date'] ?? now(),
                    'type'            => $data['inspection_type']
                        ?? ($hasNewImages
                            ? InspectionType::PERIODIC->value
                            : InspectionType::RESOLUTION->value),
                    'description'     => $data['inspection_description']
                        ?? 'Atualização via edição cadastral.',
                    'images'          => $data['images'] ?? []
                ]);
            }

            return $material->fresh();
        });
    }

    public function toggleActive(
        AccessibleEducationalMaterial $material
    ): AccessibleEducationalMaterial {
        $material->update(['is_active' => !$material->is_active]);
        return $material;
    }

    public function delete(AccessibleEducationalMaterial $material): void
    {
        DB::transaction(function () use ($material) {
            $hasOpenLoans = $material->loans()
                ->whereNull('return_date')
                ->exists();

            if ($hasOpenLoans) {
                throw ValidationException::withMessages([
                    'delete' =>
                        'Não é possível excluir: este material possui empréstimos pendentes.'
                ]);
            }

            $material->delete();
        });
    }
}
