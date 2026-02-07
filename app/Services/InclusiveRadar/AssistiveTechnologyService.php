<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\InspectionType;
use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoans;
use App\Models\InclusiveRadar\{AssistiveTechnology, ResourceType};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssistiveTechnologyService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected ResourceAttributeValueService $attributeValueService,
    ) {}

    // Leitura

    public function index(): Collection
    {
        return AssistiveTechnology::with(['type', 'resourceStatus', 'deficiencies'])
            ->orderBy('name')
            ->get();
    }

    // Ações principais

    public function store(array $data): AssistiveTechnology
    {
        return DB::transaction(
            fn() => $this->persist(new AssistiveTechnology(), $data)
        );
    }

    public function update(AssistiveTechnology $assistiveTechnology, array $data): AssistiveTechnology
    {
        return DB::transaction(
            fn() => $this->persist($assistiveTechnology, $data)
        );
    }

    public function toggleActive(AssistiveTechnology $assistiveTechnology): AssistiveTechnology
    {
        $assistiveTechnology->update([
            'is_active' => !$assistiveTechnology->is_active
        ]);

        return $assistiveTechnology;
    }

    public function delete(AssistiveTechnology $assistiveTechnology): void
    {
        DB::transaction(function () use ($assistiveTechnology) {

            if ($assistiveTechnology->loans()->whereNull('return_date')->exists()) {
                throw new CannotDeleteWithActiveLoans();
            }

            $assistiveTechnology->delete();
        });
    }

    // Regras internas

    protected function persist(AssistiveTechnology $assistiveTechnology, array $data): AssistiveTechnology
    {
        // Valida regras de negócio, recalcula estoque, salva,
        // sincroniza relações e registra vistoria se necessário
        $this->validateBusinessRules($assistiveTechnology, $data);

        $data = $this->calculateStock($assistiveTechnology, $data);

        $assistiveTechnology->fill($data)->save();

        $this->syncRelations($assistiveTechnology, $data);
        $this->handleInspectionLog($assistiveTechnology, $data);

        return $assistiveTechnology->fresh([
            'type',
            'resourceStatus',
            'deficiencies'
        ]);
    }

    protected function validateBusinessRules(AssistiveTechnology $assistiveTechnology, array $data): void
    {

        if ($assistiveTechnology->exists && isset($data['quantity'])) {

            $activeLoans = $assistiveTechnology
                ->loans()
                ->whereIn('status', ['active', 'late'])
                ->count();

            // Impede reduzir quantidade abaixo do que já está emprestado
            if ((int)$data['quantity'] < $activeLoans) {
                throw ValidationException::withMessages([
                    'quantity' => "Mínimo permitido: {$activeLoans} (recursos atualmente em uso)."
                ]);
            }
        }
    }

    protected function calculateStock(AssistiveTechnology $assistiveTechnology, array $data): array
    {

        $type = ResourceType::find(
            $data['type_id'] ?? $assistiveTechnology->type_id
        );

        // Recursos digitais não possuem controle de estoque
        if ($type?->is_digital) {
            $data['quantity'] = null;
            $data['quantity_available'] = null;
            return $data;
        }

        $total = (int) (
            $data['quantity']
            ?? $assistiveTechnology->quantity
            ?? 0
        );

        $activeLoans = $assistiveTechnology->exists
            ? $assistiveTechnology
                ->loans()
                ->whereIn('status', ['active', 'late'])
                ->count()
            : 0;

        $data['quantity_available'] = $total - $activeLoans;

        return $data;
    }

    protected function handleInspectionLog(AssistiveTechnology $assistiveTechnology, array $data): void
    {

        $isUpdate = $assistiveTechnology->wasRecentlyCreated === false;

        // Evita criar vistoria quando nada relevante foi alterado
        if (
            $isUpdate
            && !$assistiveTechnology->wasChanged('conservation_state')
            && empty($data['inspection_description'])
            && empty($data['images'])
        ) {
            return;
        }

        $this->inspectionService->createForModel(
            $assistiveTechnology,
            [
                'state' => $assistiveTechnology->conservation_state,
                'inspection_date' => $data['inspection_date'] ?? now(),
                'type' => $data['inspection_type']
                    ?? ($isUpdate
                        ? InspectionType::PERIODIC->value
                        : InspectionType::INITIAL->value),
                'description' => $data['inspection_description']
                    ?? ($isUpdate
                        ? 'Atualização de estado via edição de material.'
                        : 'Vistoria inicial de entrada.'),
                'images' => $data['images'] ?? []
            ]
        );
    }

    protected function syncRelations(AssistiveTechnology $assistiveTechnology, array $data): void
    {

        // Sincroniza relação com deficiências vinculadas
        if (isset($data['deficiencies'])) {
            $assistiveTechnology
                ->deficiencies()
                ->sync($data['deficiencies']);
        }

        // Persiste valores de atributos dinâmicos do recurso
        if (isset($data['attributes'])) {
            $this->attributeValueService
                ->saveValues($assistiveTechnology, $data['attributes']);
        }
    }
}
