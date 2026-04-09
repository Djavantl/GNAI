<?php

namespace App\Services\InclusiveRadar;

use App\Audit\AuditLogger;
use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Enums\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use DomainException;

class AssistiveTechnologyService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected LoanService $loanService,
        protected AuditLogger $auditLogger,
    ) {}

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

    public function delete(AssistiveTechnology $assistiveTechnology): void
    {
        DB::transaction(function () use ($assistiveTechnology) {
            /* Impedimos a exclusão para manter a integridade histórica dos
               empréstimos ativos e evitar órfãos no sistema de rastreio. */
            if ($assistiveTechnology->loans()->whereNull('return_date')->exists()) {
                throw new BusinessRuleException("Não é possível excluir um item com empréstimos ativos.");

            }
            $assistiveTechnology->delete();
        });
    }

    protected function persist(AssistiveTechnology $at, array $data): AssistiveTechnology
    {
        $this->validateBusinessRules($at, $data);

        $oldDef = $this->captureOriginalState($at);

        $data = $this->processStock($at, $data);

        $this->validateStatusChangeWithActiveLoans($at, $data);

        $this->saveModel($at, $data);

        $this->syncRelations($at, $data);

        $this->logRelationChanges($at, $data, $oldDef);

        $this->runInspection($at, $data);

        return $this->loadFreshRelations($at);
    }

    private function validateBusinessRules(AssistiveTechnology $at, array $data): void
    {
        $isDigital = $data['is_digital'] ?? $at->is_digital ?? false;
        $isLoanable = $data['is_loanable'] ?? $at->is_loanable ?? false;
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : $at->quantity;
        $available = isset($data['quantity_available']) ? (int)$data['quantity_available'] : $at->quantity_available;

        if (isset($data['deficiencies']) && empty($data['deficiencies'])) {
            throw new InvalidArgumentException("Selecione pelo menos um público-alvo.");
        }

        if (!$isDigital && $quantity <= 0) {
            throw new DomainException("Para recursos físicos, a quantidade deve ser no mínimo 1.");
        }

        if ($isLoanable && $quantity <= 0) {
            throw new DomainException("Recursos marcados como emprestáveis devem ter quantidade maior que zero.");
        }

        if ($available > $quantity) {
            throw new DomainException("A quantidade disponível ({$available}) não pode ser maior que a quantidade total ({$quantity}).");
        }
    }

    private function captureOriginalState(AssistiveTechnology $at): array
    {
        return $at->exists
            ? $at->deficiencies()->pluck('deficiencies.id')->toArray()
            : [];
    }

    private function processStock(AssistiveTechnology $at, array $data): array
    {
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($at, (int)$data['quantity']);
        }

        return $this->loanService->calculateStockForLoan($at, $data);
    }

    private function saveModel(AssistiveTechnology $at, array $data): void
    {
        if (!$at->exists && empty($data['status'])) {
            $data['status'] = ResourceStatus::AVAILABLE->value;
        }

        $at->fill($data)->save();
    }

    protected function syncRelations(AssistiveTechnology $at, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $at->deficiencies()->sync($data['deficiencies']);
        }
    }

    private function validateStatusChangeWithActiveLoans(AssistiveTechnology $at, array $data): void
    {
        if (!$at->exists || !isset($data['status'])) return;

        $hasActiveLoans = $at->loans()->whereNull('return_date')->exists();

        /* Bloqueamos a mudança de status (ex: para Manutenção) se houver
           empréstimos em aberto para evitar inconsistência no inventário. */
        if ($hasActiveLoans && $at->status->value != $data['status']) {
            throw new BusinessRuleException("Não é possível alterar o status do item enquanto houver empréstimos ativos.");
        }
    }

    private function runInspection(AssistiveTechnology $at, array $data): void
    {
        $this->inspectionService->createInspectionForModel($at, $data);
    }

    private function loadFreshRelations(AssistiveTechnology $at): AssistiveTechnology
    {
        return $at->fresh(['deficiencies']);
    }

    private function logRelationChanges(AssistiveTechnology $at, array $data, array $oldDef): void
    {
        if ($at->wasRecentlyCreated) return;

        if (isset($data['deficiencies'])) {
            $this->auditLogger->logRelationIfChanged(
                $at,
                'deficiencies',
                $oldDef,
                array_map('intval', $data['deficiencies'])
            );
        }
    }
}
