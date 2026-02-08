<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoans;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use Illuminate\Support\Facades\DB;

class AccessibleEducationalMaterialService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected ResourceAttributeValueService $attributeValueService,
        protected LoanService $loanService,
    ) {}

    // Ações principais

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

    public function toggleActive(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($material) {

            $material->update([
                'is_active' => !$material->is_active
            ]);

            return $material;
        });
    }

    public function delete(AccessibleEducationalMaterial $material): void
    {
        DB::transaction(function () use ($material) {

            if ($material->loans()->whereNull('return_date')->exists()) {
                throw new CannotDeleteWithActiveLoans();
            }

            $material->delete();
        });
    }

    // Regras internas

    protected function persist(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        // Valida se a quantidade informada não é menor do que os recursos atualmente emprestados
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($material, (int)$data['quantity']);
        }

        // Recalcula o estoque disponível considerando empréstimos ativos
        $data = $this->loanService->calculateStockForLoan($material, $data);

        // Preenche os dados do modelo e salva no banco
        $material->fill($data)->save();

        // Sincroniza relações como deficiências vinculadas e atributos dinâmicos
        $this->syncRelations($material, $data);

        // Cria uma vistoria caso necessário, usando o serviço de inspeção genérico
        $this->inspectionService->createInspectionForModel($material, $data);

        return $material->fresh([
            'type',
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures'
        ]);
    }

    protected function syncRelations(AccessibleEducationalMaterial $material, array $data): void
    {
        // Sincroniza relação com deficiências vinculadas
        if (isset($data['deficiencies'])) {
            $material
                ->deficiencies()
                ->sync($data['deficiencies']);
        }

        // Sincroniza relação com funcionalidades de acessibilidade
        if (isset($data['accessibility_features'])) {
            $material
                ->accessibilityFeatures()
                ->sync($data['accessibility_features']);
        }

        // Persiste valores de atributos dinâmicos do recurso
        if (isset($data['attributes'])) {
            $this->attributeValueService
                ->saveValues($material, $data['attributes']);
        }
    }
}
