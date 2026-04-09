<?php

namespace App\Services\InclusiveRadar;

use App\Audit\AuditLogger;
use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Enums\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;
use DomainException;
use InvalidArgumentException;

class AccessibleEducationalMaterialService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected LoanService $loanService,
        protected AuditLogger $auditLogger,
    ) {}

    public function store(array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(
            fn() => $this->persist(new AccessibleEducationalMaterial(), $data)
        );
    }

    public function update(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(
            fn() => $this->persist($material, $data)
        );
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

    protected function persist(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        $this->validateBusinessRules($material, $data);

        [$oldDef, $oldFeatures] = $this->captureOriginalState($material);

        $data = $this->processStock($material, $data);

        $this->validateStatusChangeWithActiveLoans($material, $data);

        $this->saveModel($material, $data);

        $this->syncRelations($material, $data);

        $this->logRelationChanges($material, $data, $oldDef, $oldFeatures);

        $this->runInspection($material, $data);

        return $this->loadFreshRelations($material);
    }

    private function validateBusinessRules(AccessibleEducationalMaterial $material, array $data): void
    {
        $isDigital = $data['is_digital'] ?? $material->is_digital ?? false;
        $isLoanable = $data['is_loanable'] ?? $material->is_loanable ?? false;

        $quantity = isset($data['quantity'])
            ? (int) $data['quantity']
            : $material->quantity;

        $available = isset($data['quantity_available'])
            ? (int) $data['quantity_available']
            : $material->quantity_available;

        if (isset($data['deficiencies']) && empty($data['deficiencies'])) {
            throw new InvalidArgumentException(
                "Selecione pelo menos um público-alvo."
            );
        }

        if (!$isDigital && $quantity <= 0) {
            throw new DomainException(
                "Para materiais físicos, a quantidade deve ser no mínimo 1."
            );
        }

        if ($isLoanable && $quantity <= 0) {
            throw new DomainException(
                "Materiais marcados como emprestáveis devem ter quantidade maior que zero."
            );
        }

        if ($available > $quantity) {
            throw new DomainException(
                "A quantidade disponível ({$available}) não pode ser maior que a quantidade total ({$quantity})."
            );
        }
    }

    private function captureOriginalState(AccessibleEducationalMaterial $material): array
    {
        $oldDeficiencies = $material->exists
            ? $material->deficiencies()->pluck('deficiencies.id')->toArray()
            : [];

        $oldFeatures = $material->exists
            ? $material->accessibilityFeatures()->pluck('accessibility_features.id')->toArray()
            : [];

        return [$oldDeficiencies, $oldFeatures];
    }

    private function processStock(AccessibleEducationalMaterial $material, array $data): array
    {
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($material, (int) $data['quantity']);
        }

        return $this->loanService->calculateStockForLoan($material, $data);
    }

    private function saveModel(AccessibleEducationalMaterial $material, array $data): void
    {
        if (!$material->exists && empty($data['status'])) {
            $data['status'] = ResourceStatus::AVAILABLE->value;
        }

        $material->fill($data)->save();
    }

    protected function syncRelations(AccessibleEducationalMaterial $material, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $material->deficiencies()->sync($data['deficiencies']);
        }

        if (isset($data['accessibility_features'])) {
            $material->accessibilityFeatures()->sync($data['accessibility_features']);
        }
    }

    private function validateStatusChangeWithActiveLoans(AccessibleEducationalMaterial $material, array $data): void
    {
        if (!$material->exists || !isset($data['status'])) return;

        $hasActiveLoans = $material->loans()->whereNull('return_date')->exists();

        /* Mudanças de status (ex: Inativo ou Manutenção) são bloqueadas se houver
           empréstimos ativos para não gerar inconsistência no fluxo de devolução. */
        if ($hasActiveLoans && $material->status->value != $data['status']) {
            throw new BusinessRuleException("Não é possível alterar o status do item enquanto houver empréstimos ativos.");
        }
    }

    private function runInspection(AccessibleEducationalMaterial $material, array $data): void
    {
        $this->inspectionService->createInspectionForModel($material, $data);
    }

    private function loadFreshRelations(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        return $material->fresh(['deficiencies', 'accessibilityFeatures']);
    }

    private function logRelationChanges(AccessibleEducationalMaterial $material, array $data, array $oldDef, array $oldFeatures): void
    {
        if ($material->wasRecentlyCreated) return;

        if (isset($data['deficiencies'])) {
            $this->auditLogger->logRelationIfChanged(
                $material,
                'deficiencies',
                $oldDef,
                array_map('intval', $data['deficiencies'])
            );
        }

        if (isset($data['accessibility_features'])) {
            $this->auditLogger->logRelationIfChanged(
                $material,
                'accessibility_features',
                $oldFeatures,
                array_map('intval', $data['accessibility_features'])
            );
        }
    }
}
