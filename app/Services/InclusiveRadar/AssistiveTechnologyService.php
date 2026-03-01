<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;

class AssistiveTechnologyService
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
                throw new CannotDeleteWithActiveLoansException();
            }

            $assistiveTechnology->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PERSISTÊNCIA CENTRAL
    |--------------------------------------------------------------------------
    */

    protected function persist(AssistiveTechnology $at, array $data): AssistiveTechnology
    {
        $oldDeficiencies = $at->exists
            ? $at->deficiencies()->pluck('deficiencies.id')->toArray()
            : [];

        $data = $this->processStock($at, $data);

        $this->saveModel($at, $data);

        $this->syncRelations($at, $data);

        $this->logDeficiencyChanges($at, $data, $oldDeficiencies);

        $this->runInspection($at, $data);

        return $this->loadFreshRelations($at);
    }

    /*
    |--------------------------------------------------------------------------
    | ESTOQUE
    |--------------------------------------------------------------------------
    */

    private function processStock(AssistiveTechnology $at, array $data): array
    {
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($at, (int)$data['quantity']);
        }

        return $this->loanService->calculateStockForLoan($at, $data);
    }

    /*
    |--------------------------------------------------------------------------
    | SALVAMENTO
    |--------------------------------------------------------------------------
    */

    private function saveModel(AssistiveTechnology $at, array $data): void
    {
        if (!$at->exists && empty($data['status_id'])) {
            $availableStatus = ResourceStatus::where('code', 'available')->first();
            if ($availableStatus) {
                $data['status_id'] = $availableStatus->id;
            }
        }

        $at->fill($data)->save();
    }

    protected function syncRelations(AssistiveTechnology $at, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $at->deficiencies()->sync($data['deficiencies']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | INSPEÇÃO
    |--------------------------------------------------------------------------
    */

    private function runInspection(AssistiveTechnology $at, array $data): void
    {
        $this->inspectionService->createInspectionForModel($at, $data);
    }

    /*
    |--------------------------------------------------------------------------
    | FRESH LOAD
    |--------------------------------------------------------------------------
    */

    private function loadFreshRelations(AssistiveTechnology $at): AssistiveTechnology
    {
        return $at->fresh([
            'resourceStatus',
            'deficiencies',
            'trainings'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AUDITORIA
    |--------------------------------------------------------------------------
    */

    private function logDeficiencyChanges(AssistiveTechnology $at, array $data, array $oldDef): void
    {
        if ($at->wasRecentlyCreated) return;

        if (isset($data['deficiencies'])) {

            $newDef = array_map('intval', $data['deficiencies']);

            sort($oldDef);
            sort($newDef);

            if ($oldDef !== $newDef) {

                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'updated',
                    'auditable_type' => $at->getMorphClass(),
                    'auditable_id' => $at->id,
                    'old_values' => ['deficiencies' => $oldDef],
                    'new_values' => ['deficiencies' => $newDef],
                    'ip_address' => request()?->ip(),
                    'user_agent' => request()?->userAgent(),
                ]);
            }
        }
    }
}
