<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\ResourceType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccessibleEducationalMaterialService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected ResourceAttributeValueService $attributeValueService,
        protected LoanService $loanService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | CRUD
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | PERSIST (Fluxo Principal)
    |--------------------------------------------------------------------------
    */

    protected function persist(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        [$oldDef, $oldFeatures, $oldAttr, $oldTrainings] = $this->captureOriginalState($material);

        $data = $this->processStock($material, $data);

        $this->validateStatusChangeWithActiveLoans($material, $data);

        $this->saveModel($material, $data);

        $this->syncRelations($material, $data);

        $this->logRelationChanges($material, $data, $oldDef, $oldFeatures, $oldAttr, $oldTrainings);

        $this->runInspection($material, $data);

        return $this->loadFreshRelations($material);
    }

    /*
    |--------------------------------------------------------------------------
    | Etapas do Persist
    |--------------------------------------------------------------------------
    */

    private function captureOriginalState(AccessibleEducationalMaterial $material): array
    {
        $oldDeficiencies = $material->exists
            ? $material->deficiencies()->pluck('deficiencies.id')->toArray()
            : [];

        $oldFeatures = $material->exists
            ? $material->accessibilityFeatures()->pluck('accessibility_features.id')->toArray()
            : [];

        $oldAttributes = $material->exists
            ? $material->attributeValues()->pluck('value', 'attribute_id')->toArray()
            : [];

        $oldTrainings = $material->exists
            ? $material->trainings()->pluck('trainings.id')->toArray()
            : [];

        return [$oldDeficiencies, $oldFeatures, $oldAttributes, $oldTrainings];
    }

    private function processStock(AccessibleEducationalMaterial $material, array $data): array
    {
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability(
                $material,
                (int) $data['quantity']
            );
        }

        return $this->loanService->calculateStockForLoan($material, $data);
    }

    private function validateStatusChangeWithActiveLoans(AccessibleEducationalMaterial $material, array $data): void
    {
        if (!$material->exists) return;

        if (!isset($data['status_id'])) return;

        $hasActiveLoans = $material->loans()->whereNull('return_date')->exists();

        if (!$hasActiveLoans) return;

        if ($material->status_id != $data['status_id']) {
            throw ValidationException::withMessages([
                'status_id' => 'Não é possível alterar o status do material enquanto houver empréstimos ativos. O sistema gerencia o status automaticamente baseado no estoque.'
            ]);
        }
    }

    private function saveModel(AccessibleEducationalMaterial $material, array $data): void
    {
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

        if (isset($data['attributes'])) {

            $type = ResourceType::find($material->type_id);

            $validAttributeIds = $type
                ? $type->attributes()->pluck('type_attributes.id')->toArray()
                : [];

            foreach ($data['attributes'] as $attributeId => $value) {
                if (empty(trim($value))) {

                    $material->attributeValues()
                        ->where('attribute_id', $attributeId)
                        ->delete();

                    unset($data['attributes'][$attributeId]);
                }
            }

            $material->attributeValues()
                ->whereNotIn('attribute_id', $validAttributeIds)
                ->delete();

            if (!empty($data['attributes'])) {
                $this->attributeValueService
                    ->saveValues($material, $data['attributes']);
            }
        }

        if (!empty($data['trainings'])) {

            $material->trainings()->delete();

            foreach ($data['trainings'] as $training) {

                $t = $material->trainings()->create([
                    'title' => $training['title'],
                    'description' => $training['description'] ?? null,
                    'url' => $training['url'] ?? null,
                    'is_active' => true
                ]);

                if (!empty($training['files'])) {

                    foreach ($training['files'] as $file) {

                        $path = $file->store('trainings','public');

                        $t->files()->create([
                            'path' => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                        ]);
                    }
                }
            }
        }
    }

    private function logRelationChanges(AccessibleEducationalMaterial $material, array $data, array $oldDef, array $oldFeatures, array $oldAttr, array $oldTrainings): void
    {
        if ($material->wasRecentlyCreated) {
            return;
        }

        if (isset($data['deficiencies'])) {
            $this->auditIfChanged($material, 'deficiencies', $oldDef, $data['deficiencies']);
        }

        if (isset($data['accessibility_features'])) {
            $this->auditIfChanged($material, 'accessibility_features', $oldFeatures, $data['accessibility_features']);
        }

        if (isset($data['attributes'])) {

            $newAttr = array_filter($data['attributes'], fn($v)=>!is_null($v));

            if ($oldAttr != $newAttr) {
                $this->logRelationChange($material, 'attributes', $oldAttr, $newAttr);
            }
        }

        if (isset($data['trainings'])) {

            $newTrain = $material->trainings()->pluck('id')->toArray();

            sort($oldTrainings);
            sort($newTrain);

            if ($oldTrainings !== $newTrain) {
                $this->logRelationChange($material, 'trainings', $oldTrainings, $newTrain);
            }
        }
    }

    private function runInspection(AccessibleEducationalMaterial $material, array $data): void
    {
        $this->inspectionService
            ->createInspectionForModel($material, $data);
    }

    private function loadFreshRelations(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        return $material->fresh([
            'type',
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures',
            'attributeValues',
            'trainings'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Utilitários
    |--------------------------------------------------------------------------
    */

    protected function auditIfChanged($model, $field, $old, $new)
    {
        if ($new === null) return;

        $new = array_map('intval', $new);

        sort($old);
        sort($new);

        if ($old !== $new) {
            $this->logRelationChange($model, $field, $old, $new);
        }
    }

    protected function logRelationChange(AccessibleEducationalMaterial $model, string $field, array $old, array $new): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->id,
            'old_values' => [$field => $old],
            'new_values' => [$field => $new],
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
