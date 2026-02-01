<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\{AssistiveTechnology, ResourceType};
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\SpecializedEducationalSupport\DeficiencyService;
use App\Enums\InclusiveRadar\{ConservationState, InspectionType};
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
        return AssistiveTechnology::with(['type', 'resourceStatus', 'deficiencies'])->orderBy('name')->get();
    }

    public function getCreateData(): array
    {
        return [
            'deficiencies' => Deficiency::where('is_active', true)->orderBy('name')->get(),
        ];
    }

    public function getEditData(AssistiveTechnology $assistiveTechnology): array
    {
        return [
            'assistiveTechnology' => $assistiveTechnology->load(['deficiencies', 'inspections.images']),
            'attributeValues' => $this->attributeValueService->getValuesForForm($assistiveTechnology),
            'deficiencies' => $this->deficiencyService->listAll(),
        ];
    }

    public function store(array $data): AssistiveTechnology
    {
        return DB::transaction(function () use ($data) {
            $type = ResourceType::find($data['type_id']);

            $data['quantity_available'] = ($type && $type->is_digital) ? null : ($data['quantity'] ?? 0);
            if ($type && $type->is_digital) $data['quantity'] = null;

            $tech = AssistiveTechnology::create($data);

            if (!empty($data['deficiencies'])) {
                $tech->deficiencies()->sync($data['deficiencies']);
            }

            $this->attributeValueService->saveValues($tech, $data['attributes'] ?? []);

            $this->inspectionService->createForModel($tech, [
                'state'           => $data['conservation_state'] ?? ConservationState::NEW->value,
                'inspection_date' => now(),
                'type'            => InspectionType::INITIAL->value,
                'description'     => $data['inspection_description'] ?? 'Vistoria inicial realizada no cadastro.',
                'images'          => $data['images'] ?? []
            ]);

            return $tech;
        });
    }

    public function update(AssistiveTechnology $assistiveTechnology, array $data): AssistiveTechnology
    {
        return DB::transaction(function () use ($assistiveTechnology, $data) {
            $type = ResourceType::find($data['type_id'] ?? $assistiveTechnology->type_id);

            if ($type && $type->is_digital) {
                $data['quantity'] = $data['quantity_available'] = null;
            } else {
                $newTotal = (int) ($data['quantity'] ?? 0);
                $activeLoans = $assistiveTechnology->loans()->whereIn('status', ['active', 'late'])->count();
                if ($newTotal < $activeLoans) {
                    throw ValidationException::withMessages(['quantity' => "Mínimo permitido: {$activeLoans}."]);
                }
                $data['quantity_available'] = $newTotal - $activeLoans;
            }

            $oldState = $assistiveTechnology->getOriginal('conservation_state');
            $newState = $data['conservation_state'] ?? $oldState;

            $assistiveTechnology->update($data);

            if (array_key_exists('deficiencies', $data)) {
                $assistiveTechnology->deficiencies()->sync($data['deficiencies'] ?? []);
            }

            $this->attributeValueService->saveValues($assistiveTechnology, $data['attributes'] ?? []);

            $hasNewImages = !empty($data['images']);
            $stateChanged = $newState !== $oldState;

            if ($hasNewImages || $stateChanged) {
                $this->inspectionService->createForModel($assistiveTechnology, [
                    'state'           => $newState,
                    'inspection_date' => $data['inspection_date'] ?? now(),
                    'type'            => $data['inspection_type'] ?? ($hasNewImages ? InspectionType::PERIODIC->value : InspectionType::RESOLUTION->value),
                    'description'     => $data['inspection_description'] ?? 'Atualização de estado via edição cadastral.',
                    'images'          => $data['images'] ?? []
                ]);
            }

            return $assistiveTechnology->fresh();
        });
    }

    public function toggleActive(AssistiveTechnology $assistiveTechnology): AssistiveTechnology
    {
        $assistiveTechnology->update(['is_active' => !$assistiveTechnology->is_active]);
        return $assistiveTechnology;
    }

    public function delete(AssistiveTechnology $assistiveTechnology): void
    {
        DB::transaction(function () use ($assistiveTechnology) {
            $hasOpenLoans = $assistiveTechnology->loans()
                ->whereNull('return_date')
                ->exists();

            if ($hasOpenLoans) {
                throw ValidationException::withMessages([
                    'delete' => 'Não é possível excluir: este recurso ainda possui empréstimos pendentes (não devolvidos).'
                ]);
            }

            $assistiveTechnology->delete();
        });
    }
}
