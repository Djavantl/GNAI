<?php

namespace App\Services\InclusiveRadar;

use App\Audit\AuditLogger;
use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Enums\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;

class AssistiveTechnologyService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected LoanService $loanService,
        protected AuditLogger $auditLogger,
    ) {}

    public function store(array $data): AssistiveTechnology
    {
        return DB::transaction(function () use ($data) {
            $at = new AssistiveTechnology();

            $this->validateBusinessRules($at, $data);

            $data['status'] = $data['status'] ?? ResourceStatus::AVAILABLE->value;
            $data = $this->loanService->calculateStockForLoan($at, $data);

            $at->fill($data)->save();

            if (isset($data['deficiencies'])) {
                $at->deficiencies()->sync($data['deficiencies']);
            }

            $this->inspectionService->createInspectionForModel($at, $data);

            return $at->fresh(['deficiencies']);
        });
    }

    public function update(AssistiveTechnology $at, array $data): AssistiveTechnology
    {
        return DB::transaction(function () use ($at, $data) {
            $this->validateBusinessRules($at, $data);
            $this->validateStatusChangeWithActiveLoans($at, $data);

            if (isset($data['quantity'])) {
                $this->loanService->validateStockAvailability($at, (int) $data['quantity']);
            }

            $oldDef = $at->deficiencies()->pluck('deficiencies.id')->toArray();

            $data = $this->loanService->calculateStockForLoan($at, $data);

            $at->fill($data)->save();

            if (isset($data['deficiencies'])) {
                $at->deficiencies()->sync($data['deficiencies']);

                $this->auditLogger->logRelationIfChanged(
                    $at,
                    'deficiencies',
                    $oldDef,
                    array_map('intval', $data['deficiencies'])
                );
            }

            $this->inspectionService->createInspectionForModel($at, $data);

            return $at->fresh(['deficiencies']);
        });
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

    private function validateBusinessRules(AssistiveTechnology $at, array $data): void
    {
        $isDigital = $data['is_digital'] ?? $at->is_digital  ?? false;
        $isLoanable = $data['is_loanable'] ?? $at->is_loanable ?? false;
        $quantity = isset($data['quantity']) ? (int) $data['quantity'] : $at->quantity;
        $available = isset($data['quantity_available']) ? (int) $data['quantity_available'] : $at->quantity_available;

        if (isset($data['deficiencies']) && empty($data['deficiencies'])) {
            throw new BusinessRuleException("Selecione pelo menos um público-alvo.");
        }

        if (!$isDigital && $quantity <= 0) {
            throw new BusinessRuleException("Para recursos físicos, a quantidade deve ser no mínimo 1.");
        }

        if ($isLoanable && $quantity <= 0) {
            throw new BusinessRuleException("Recursos marcados como emprestáveis devem ter quantidade maior que zero.");
        }

        if ($available > $quantity) {
            throw new BusinessRuleException("A quantidade disponível ({$available}) não pode ser maior que a quantidade total ({$quantity}).");
        }
    }

    private function validateStatusChangeWithActiveLoans(AssistiveTechnology $at, array $data): void
    {
        if (!isset($data['status'])) return;

        /* Bloqueamos a mudança de status (ex: para Manutenção) se houver
           empréstimos em aberto para evitar inconsistência no inventário. */
        if ($at->loans()->whereNull('return_date')->exists() && $at->status->value !== $data['status']) {
            throw new BusinessRuleException("Não é possível alterar o status do item enquanto houver empréstimos ativos.");
        }
    }
}
