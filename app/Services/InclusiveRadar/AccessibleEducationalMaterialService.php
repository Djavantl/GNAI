<?php

namespace App\Services\InclusiveRadar;

use App\Audit\AuditLogger;
use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Enums\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;

class AccessibleEducationalMaterialService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected LoanService $loanService,
        protected AuditLogger $auditLogger,
    ) {}

    public function store(array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($data) {
            $material = new AccessibleEducationalMaterial();

            $this->validateBusinessRules($material, $data);

            $data['status'] = $data['status'] ?? ResourceStatus::AVAILABLE->value;
            $data = $this->loanService->calculateStockForLoan($material, $data);

            $material->fill($data)->save();

            if (isset($data['deficiencies'])) {
                $material->deficiencies()->sync($data['deficiencies']);
            }

            if (isset($data['accessibility_features'])) {
                $material->accessibilityFeatures()->sync($data['accessibility_features']);
            }

            $this->inspectionService->createInspectionForModel($material, $data);

            return $material->fresh(['deficiencies', 'accessibilityFeatures']);
        });
    }

    public function update(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($material, $data) {
            $this->validateBusinessRules($material, $data);
            $this->validateStatusChangeWithActiveLoans($material, $data);

            if (isset($data['quantity'])) {
                $this->loanService->validateStockAvailability($material, (int) $data['quantity']);
            }

            $oldDef = $material->deficiencies()->pluck('deficiencies.id')->toArray();
            $oldFeatures = $material->accessibilityFeatures()->pluck('accessibility_features.id')->toArray();

            $data = $this->loanService->calculateStockForLoan($material, $data);

            $material->fill($data)->save();

            if (isset($data['deficiencies'])) {
                $material->deficiencies()->sync($data['deficiencies']);

                $this->auditLogger->logRelationIfChanged(
                    $material,
                    'deficiencies',
                    $oldDef,
                    array_map('intval', $data['deficiencies'])
                );
            }

            if (isset($data['accessibility_features'])) {
                $material->accessibilityFeatures()->sync($data['accessibility_features']);

                $this->auditLogger->logRelationIfChanged(
                    $material,
                    'accessibility_features',
                    $oldFeatures,
                    array_map('intval', $data['accessibility_features'])
                );
            }

            $this->inspectionService->createInspectionForModel($material, $data);

            return $material->fresh(['deficiencies', 'accessibilityFeatures']);
        });
    }

    public function delete(AccessibleEducationalMaterial $material): void
    {
        DB::transaction(function () use ($material) {
            /* Impedimos a remoção para evitar a perda do histórico de movimentação
               de itens que ainda estão sob posse de terceiros. */
            if ($material->loans()->whereNull('return_date')->exists()) {
                throw new BusinessRuleException("Não é possível excluir um item com empréstimos ativos.");
            }

            $material->delete();
        });
    }

    private function validateBusinessRules(AccessibleEducationalMaterial $material, array $data): void
    {
        $isDigital = $data['is_digital']  ?? $material->is_digital  ?? false;
        $isLoanable = $data['is_loanable'] ?? $material->is_loanable ?? false;
        $quantity = isset($data['quantity']) ? (int) $data['quantity'] : $material->quantity;
        $available = isset($data['quantity_available']) ? (int) $data['quantity_available'] : $material->quantity_available;

        if (isset($data['deficiencies']) && empty($data['deficiencies'])) {
            throw new BusinessRuleException("Selecione pelo menos um público-alvo.");
        }

        if (!$isDigital && $quantity <= 0) {
            throw new BusinessRuleException("Para materiais físicos, a quantidade deve ser no mínimo 1.");
        }

        if ($isLoanable && $quantity <= 0) {
            throw new BusinessRuleException("Materiais marcados como emprestáveis devem ter quantidade maior que zero.");
        }

        if ($available > $quantity) {
            throw new BusinessRuleException("A quantidade disponível ({$available}) não pode ser maior que a quantidade total ({$quantity}).");
        }
    }

    private function validateStatusChangeWithActiveLoans(AccessibleEducationalMaterial $material, array $data): void
    {
        if (!isset($data['status'])) return;

        /* Mudanças de status (ex: Inativo ou Manutenção) são bloqueadas se houver
           empréstimos ativos para não gerar inconsistência no fluxo de devolução. */
        if ($material->loans()->whereNull('return_date')->exists() && $material->status->value !== $data['status']) {
            throw new BusinessRuleException("Não é possível alterar o status do item enquanto houver empréstimos ativos.");
        }
    }
}
