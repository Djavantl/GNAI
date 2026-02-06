<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\InspectionType;
use App\Models\InclusiveRadar\{AssistiveTechnology, ResourceType};
use App\Services\SpecializedEducationalSupport\DeficiencyService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssistiveTechnologyService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected ResourceAttributeValueService $attributeValueService,
        protected DeficiencyService $deficiencyService
    ) {}

    public function listAll(): Collection
    {
        return AssistiveTechnology::with(['type', 'resourceStatus', 'deficiencies'])
            ->orderBy('name')
            ->get();
    }

    public function getCreateData(): array
    {
        return [
            'deficiencies' => $this->deficiencyService->listActiveOrdered(),
            'resourceTypes' => ResourceType::active()->forAssistiveTechnology()->orderBy('name')->get(),
        ];
    }

    public function getEditData(AssistiveTechnology $tech): array
    {
        return [
            'assistiveTechnology' => $tech->load(['deficiencies', 'inspections.images']),
            'deficiencies' => $this->deficiencyService->index(),
            'attributeValues' => $this->attributeValueService->getValuesForForm($tech),
            'resourceTypes' => ResourceType::active()->forAssistiveTechnology()->orderBy('name')->get(),
        ];
    }

    public function store(array $data): AssistiveTechnology
    {
        return DB::transaction(fn() => $this->persist(new AssistiveTechnology(), $data));
    }

    public function update(AssistiveTechnology $tech, array $data): AssistiveTechnology
    {
        return DB::transaction(fn() => $this->persist($tech, $data));
    }

    public function toggleActive(AssistiveTechnology $tech): AssistiveTechnology
    {
        $tech->update(['is_active' => !$tech->is_active]);
        return $tech;
    }

    public function delete(AssistiveTechnology $tech): void
    {
        DB::transaction(function () use ($tech) {
            if ($tech->loans()->whereNull('return_date')->exists()) {
                throw ValidationException::withMessages([
                    'delete' => 'Não é possível excluir: este recurso ainda possui empréstimos pendentes.'
                ]);
            }
            $tech->delete();
        });
    }

    protected function persist(AssistiveTechnology $tech, array $data): AssistiveTechnology
    {

        $this->ensureBusinessRules($tech, $data);

        $data = $this->calculateStock($tech, $data);
        $tech->fill($data)->save();

        $this->syncRelations($tech, $data);
        $this->handleInspectionLog($tech, $data);

        return $tech->fresh(['type', 'resourceStatus', 'deficiencies']);
    }

    protected function ensureBusinessRules(AssistiveTechnology $tech, array $data): void
    {
        if ($tech->exists && isset($data['quantity'])) {
            $activeLoans = $tech->loans()->whereIn('status', ['active', 'late'])->count();
            if ((int)$data['quantity'] < $activeLoans) {
                throw ValidationException::withMessages([
                    'quantity' => "Mínimo permitido: {$activeLoans} (recursos atualmente em uso)."
                ]);
            }
        }
    }

    protected function calculateStock(AssistiveTechnology $tech, array $data): array
    {
        $type = ResourceType::find($data['type_id'] ?? $tech->type_id);

        if ($type?->is_digital) {
            $data['quantity'] = $data['quantity_available'] = null;
            return $data;
        }

        $total = (int) ($data['quantity'] ?? $tech->quantity ?? 0);
        $activeLoans = $tech->exists ? $tech->loans()->whereIn('status', ['active', 'late'])->count() : 0;

        $data['quantity_available'] = $total - $activeLoans;

        return $data;
    }

    protected function handleInspectionLog(AssistiveTechnology $tech, array $data): void
    {
        $isUpdate = $tech->wasRecentlyCreated === false;

        if ($isUpdate && !$tech->wasChanged('conservation_state') && empty($data['inspection_description']) && empty($data['images'])) {
            return;
        }

        $this->inspectionService->createForModel($tech, [
            'state' => $tech->conservation_state,
            'inspection_date' => $data['inspection_date'] ?? now(),
            'type' => $data['inspection_type'] ?? ($isUpdate ? InspectionType::PERIODIC->value : InspectionType::INITIAL->value),
            'description' => $data['inspection_description'] ?? ($isUpdate
                    ? 'Atualização de estado via edição de material.'
                    : 'Vistoria inicial de entrada.'),
            'images' => $data['images'] ?? []
        ]);
    }

    protected function syncRelations(AssistiveTechnology $tech, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $tech->deficiencies()->sync($data['deficiencies']);
        }

        if (isset($data['attributes'])) {
            $this->attributeValueService->saveValues($tech, $data['attributes']);
        }
    }
}
