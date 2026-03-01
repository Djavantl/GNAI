<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;

class AccessibleEducationalMaterialService
{
    public function __construct(
        protected InspectionService $inspectionService,
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
    | PERSISTÊNCIA CENTRAL
    |--------------------------------------------------------------------------
    */

    protected function persist(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        $oldDeficiencies = $material->exists
            ? $material->deficiencies()->pluck('deficiencies.id')->toArray()
            : [];

        $oldFeatures = $material->exists
            ? $material->accessibilityFeatures()->pluck('accessibility_features.id')->toArray()
            : [];

        $data = $this->processStock($material, $data);

        $this->saveModel($material, $data);

        $this->syncRelations($material, $data);

        $this->logChanges($material, $data, $oldDeficiencies, $oldFeatures);

        $this->runInspection($material, $data);

        return $this->loadFreshRelations($material);
    }

    /*
    |--------------------------------------------------------------------------
    | ESTOQUE
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | SALVAMENTO
    |--------------------------------------------------------------------------
    */

    private function saveModel(AccessibleEducationalMaterial $material, array $data): void
    {
        if (!$material->exists && empty($data['status_id'])) {
            $availableStatus = ResourceStatus::where('code', 'available')->first();
            if ($availableStatus) {
                $data['status_id'] = $availableStatus->id;
            }
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

    /*
    |--------------------------------------------------------------------------
    | INSPEÇÃO
    |--------------------------------------------------------------------------
    */

    private function runInspection(AccessibleEducationalMaterial $material, array $data): void
    {
        $this->inspectionService->createInspectionForModel($material, $data);
    }

    /*
    |--------------------------------------------------------------------------
    | FRESH LOAD
    |--------------------------------------------------------------------------
    */

    private function loadFreshRelations(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        return $material->fresh([
            'resourceStatus',
            'deficiencies',
            'accessibilityFeatures'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AUDITORIA
    |--------------------------------------------------------------------------
    */

    private function logChanges(
        AccessibleEducationalMaterial $material,
        array $data,
        array $oldDef,
        array $oldFeatures
    ): void {
        if ($material->wasRecentlyCreated) return;

        if (isset($data['deficiencies'])) {
            $this->auditIfChanged($material, 'deficiencies', $oldDef, $data['deficiencies']);
        }

        if (isset($data['accessibility_features'])) {
            $this->auditIfChanged($material, 'accessibility_features', $oldFeatures, $data['accessibility_features']);
        }
    }

    protected function auditIfChanged($model, $field, $old, $new): void
    {
        if ($new === null) return;

        $new = array_map('intval', $new);

        sort($old);
        sort($new);

        if ($old !== $new) {
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
}
