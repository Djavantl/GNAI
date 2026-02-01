<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\{AccessibleEducationalMaterial, ResourceType};
use App\Enums\InclusiveRadar\InspectionType;
use App\Services\SpecializedEducationalSupport\DeficiencyService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccessibleEducationalMaterialService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected ResourceAttributeValueService $attributeValueService,
        protected DeficiencyService $deficiencyService
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
            'deficiencies' => $this->deficiencyService->listActiveOrdered(),
            'resourceTypes' => ResourceType::active()->forEducationalMaterial()->orderBy('name')->get(),
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
            'deficiencies' => $this->deficiencyService->listAll(),
            'attributeValues' => $this->attributeValueService->getValuesForForm($material),
            'resourceTypes' => ResourceType::active()->forEducationalMaterial()->orderBy('name')->get(),
        ];
    }

    public function store(array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(fn() => $this->persist(new AccessibleEducationalMaterial(), $data));
    }

    public function update(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(fn() => $this->persist($material, $data));
    }

    public function toggleActive(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        $material->update(['is_active' => !$material->is_active]);
        return $material;
    }

    public function delete(AccessibleEducationalMaterial $material): void
    {
        DB::transaction(function () use ($material) {
            if ($material->loans()->whereNull('return_date')->exists()) {
                throw ValidationException::withMessages([
                    'delete' => 'Não é possível excluir: este material possui empréstimos pendentes.'
                ]);
            }
            $material->delete();
        });
    }

    protected function persist(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        $this->ensureBusinessRules($material, $data);

        $data = $this->calculateStock($material, $data);
        $material->fill($data)->save();

        $this->syncRelations($material, $data);
        $this->handleInspectionLog($material, $data);

        return $material->fresh(['type', 'resourceStatus', 'deficiencies', 'accessibilityFeatures']);
    }

    protected function ensureBusinessRules(AccessibleEducationalMaterial $material, array $data): void
    {
        if ($material->exists && isset($data['quantity'])) {
            $activeLoans = $material->loans()->whereIn('status', ['active', 'late'])->count();
            if ((int)$data['quantity'] < $activeLoans) {
                throw ValidationException::withMessages([
                    'quantity' => "Mínimo permitido: {$activeLoans} (material atualmente em uso)."
                ]);
            }
        }
    }

    protected function calculateStock(AccessibleEducationalMaterial $material, array $data): array
    {
        $type = ResourceType::find($data['type_id'] ?? $material->type_id);

        if ($type?->is_digital) {
            $data['quantity'] = $data['quantity_available'] = null;
            return $data;
        }

        $total = (int) ($data['quantity'] ?? $material->quantity ?? 0);
        $activeLoans = $material->exists ? $material->loans()->whereIn('status', ['active', 'late'])->count() : 0;

        $data['quantity_available'] = $total - $activeLoans;

        return $data;
    }

    protected function handleInspectionLog(AccessibleEducationalMaterial $material, array $data): void
    {
        $isUpdate = $material->wasRecentlyCreated === false;

        if ($isUpdate && !$material->wasChanged('conservation_state') && empty($data['inspection_description']) && empty($data['images'])) {
            return;
        }

        $this->inspectionService->createForModel($material, [
            'state' => $material->conservation_state,
            'inspection_date' => $data['inspection_date'] ?? now(),
            'type' => $data['inspection_type'] ?? ($isUpdate ? InspectionType::PERIODIC->value : InspectionType::INITIAL->value),
            'description' => $data['inspection_description'] ?? ($isUpdate
                    ? 'Atualização de estado via edição de material.'
                    : 'Vistoria inicial de cadastro.'),
            'images' => $data['images'] ?? []
        ]);
    }

    protected function syncRelations(AccessibleEducationalMaterial $material, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $material->deficiencies()->sync($data['deficiencies']);
        }

        if (isset($data['accessibility_features'])) {
            $material->accessibilityFeatures()->sync($data['accessibility_features']);
        }

        if (isset($data['attributes'])) {
            $this->attributeValueService->saveValues($material, $data['attributes']);
        }
    }
}
