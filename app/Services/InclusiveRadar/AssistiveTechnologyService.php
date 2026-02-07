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
        protected LoanService $loanService,
    ) {}

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
        return DB::transaction(function () use ($assistiveTechnology) {

            $assistiveTechnology->update([
                'is_active' => !$assistiveTechnology->is_active
            ]);

            return $assistiveTechnology;
        });
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
        // Valida se a quantidade informada não é menor do que os recursos atualmente emprestados
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($assistiveTechnology, (int)$data['quantity']);
        }

        // Recalcula o estoque disponível considerando empréstimos ativos
        $data = $this->loanService->calculateStockForLoan($assistiveTechnology, $data);

        // Preenche os dados do modelo e salva no banco
        $assistiveTechnology->fill($data)->save();

        // Sincroniza relações como deficiências vinculadas e atributos dinâmicos
        $this->syncRelations($assistiveTechnology, $data);

        // Cria uma vistoria caso necessário, usando o serviço de inspeção genérico
        $this->inspectionService->createInspectionForModel($assistiveTechnology, $data);

        return $assistiveTechnology->fresh([
            'type',
            'resourceStatus',
            'deficiencies'
        ]);
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
