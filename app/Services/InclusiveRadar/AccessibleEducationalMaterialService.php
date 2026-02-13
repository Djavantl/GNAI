<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\ResourceType;
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
                throw new CannotDeleteWithActiveLoansException();
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
        // Sincroniza relação com deficiências
        if (isset($data['deficiencies'])) {
            $material->deficiencies()->sync($data['deficiencies']);
        }

        // Sincroniza funcionalidades de acessibilidade
        if (isset($data['accessibility_features'])) {
            $material->accessibilityFeatures()->sync($data['accessibility_features']);
        }

        // Persiste atributos dinâmicos
        if (isset($data['attributes'])) {

            $type = ResourceType::find($material->type_id);

            $validAttributeIds = $type
                ? $type->attributes()
                    ->pluck('type_attributes.id')
                    ->toArray()
                : [];

            // Remove valores que não pertencem mais ao tipo
            $material
                ->attributeValues()
                ->whereNotIn('attribute_id', $validAttributeIds)
                ->delete();

            // Salva novos valores
            $this->attributeValueService
                ->saveValues($material, $data['attributes']);
        }
    }
}
