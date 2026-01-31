<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\{AssistiveTechnology, ResourceType, ResourceStatus};
use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssistiveTechnologyService
{
    public function __construct(
        protected AssistiveTechnologyImageService $imageService,
        protected ResourceAttributeValueService $attributeValueService
    ) {}

    public function listAll(): Collection
    {
        return AssistiveTechnology::with(['type', 'resourceStatus', 'deficiencies', 'images'])
            ->orderBy('name')
            ->get();
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
            'assistiveTechnology' => $assistiveTechnology->load(['deficiencies', 'images']),
            'attributeValues' => [],
        ];
    }

    public function store(array $data): AssistiveTechnology
    {
        return DB::transaction(function () use ($data) {
            $type = ResourceType::find($data['type_id']);

            $data['quantity_available'] = ($type && $type->is_digital) ? null : ($data['quantity'] ?? 0);
            if ($type && $type->is_digital) $data['quantity'] = null;

            $tech = AssistiveTechnology::create($data);

            if (!empty($data['deficiencies'])) $tech->deficiencies()->sync($data['deficiencies']);

            $this->attributeValueService->saveValues('assistive_technology', $tech->id, $data['attributes'] ?? []);

            if (!empty($data['images'])) {
                foreach ($data['images'] as $img) $this->imageService->store($tech, $img);
            }

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
                    throw ValidationException::withMessages(['quantity' => "Mínimo permitido: {$activeLoans} (empréstimos ativos)."]);
                }

                $data['quantity_available'] = $newTotal - $activeLoans;

                if ($data['quantity_available'] > 0) {
                    $status = ResourceStatus::where('code', 'available')->first();
                    if ($status) $data['status_id'] = $status->id;
                }
            }

            $assistiveTechnology->update($data);

            if (array_key_exists('deficiencies', $data)) $assistiveTechnology->deficiencies()->sync($data['deficiencies'] ?? []);

            $this->attributeValueService->saveValues('assistive_technology', $assistiveTechnology->id, $data['attributes'] ?? []);

            if (!empty($data['images'])) {
                foreach ($data['images'] as $img) $this->imageService->store($assistiveTechnology, $img);
            }

            return $assistiveTechnology->fresh();
        });
    }

    public function toggleActive(AssistiveTechnology $assistiveTechnology): AssistiveTechnology
    {
        return DB::transaction(function () use ($assistiveTechnology) {
            $assistiveTechnology->update(['is_active' => !$assistiveTechnology->is_active]);
            return $assistiveTechnology;
        });
    }

    public function delete(AssistiveTechnology $assistiveTechnology): void
    {
        DB::transaction(function () use ($assistiveTechnology) {
            $hasOpenLoans = $assistiveTechnology->loans()
                ->whereNull('return_date')
                ->exists();

            if ($hasOpenLoans) {
                throw ValidationException::withMessages([
                    'delete' => 'Não é possível excluir: este recurso ainda não foi devolvido por um beneficiário.'
                ]);
            }

            $assistiveTechnology->delete();
        });
    }

    public function getItemHistory(AssistiveTechnology $assistiveTechnology): Collection
    {
        return $assistiveTechnology->loans()
            ->with(['student.person', 'professional.person'])
            ->orderByDesc('loan_date')
            ->get();
    }
}
