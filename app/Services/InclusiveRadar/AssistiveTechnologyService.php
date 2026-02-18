<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\ResourceType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssistiveTechnologyService
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
    | PERSIST (Fluxo Principal)
    |--------------------------------------------------------------------------
    */

    protected function persist(AssistiveTechnology $at, array $data): AssistiveTechnology
    {
        [$oldDef, $oldAttr, $oldTrainings] = $this->captureOriginalState($at);

        $data = $this->processStock($at, $data);

        $this->validateStatusChangeWithActiveLoans($at, $data);

        $this->saveModel($at, $data);

        $this->syncRelations($at, $data);

        $this->logRelationChanges($at, $data, $oldDef, $oldAttr, $oldTrainings);

        $this->runInspection($at, $data);

        return $this->loadFreshRelations($at);
    }

    /*
    |--------------------------------------------------------------------------
    | Etapas do Persist
    |--------------------------------------------------------------------------
    */

    private function captureOriginalState(AssistiveTechnology $at): array
    {
        $oldDeficiencies = $at->exists
            ? $at->deficiencies()->pluck('deficiencies.id')->toArray()
            : [];

        $oldAttributes = $at->exists
            ? $at->attributeValues()->pluck('value', 'attribute_id')->toArray()
            : [];

        $oldTrainings = $at->exists
            ? $at->trainings()->pluck('trainings.id')->toArray()
            : [];

        return [$oldDeficiencies, $oldAttributes, $oldTrainings];
    }

    private function processStock(AssistiveTechnology $at, array $data): array
    {
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($at,(int)$data['quantity']);
        }

        return $this->loanService->calculateStockForLoan($at,$data);
    }

    private function saveModel(AssistiveTechnology $at, array $data): void
    {
        $at->fill($data)->save();
    }

    protected function syncRelations(AssistiveTechnology $at, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $at->deficiencies()->sync($data['deficiencies']);
        }

        if (isset($data['attributes'])) {

            $type = ResourceType::find($at->type_id);

            $valid = $type
                ? $type->attributes()->pluck('type_attributes.id')->toArray()
                : [];

            foreach ($data['attributes'] as $id => $value) {
                if (empty(trim($value))) {
                    $at->attributeValues()->where('attribute_id',$id)->delete();
                    unset($data['attributes'][$id]);
                }
            }

            $at->attributeValues()
                ->whereNotIn('attribute_id',$valid)
                ->delete();

            if (!empty($data['attributes'])) {
                $this->attributeValueService->saveValues($at,$data['attributes']);
            }
        }

        // ===== TRAININGS =====

        if (!empty($data['trainings'])) {

            $at->trainings()->delete();

            foreach ($data['trainings'] as $training) {

                $t = $at->trainings()->create([
                    'title'=>$training['title'],
                    'description'=>$training['description'] ?? null,
                    'url'=>$training['url'] ?? null,
                    'is_active'=>true
                ]);

                if (!empty($training['files'])) {

                    foreach ($training['files'] as $file) {

                        $path = $file->store('trainings','public');

                        $t->files()->create([
                            'path'=>$path,
                            'original_name'=>$file->getClientOriginalName(),
                            'mime_type'=>$file->getMimeType(),
                            'size'=>$file->getSize(),
                        ]);
                    }
                }
            }
        }
    }

    private function logRelationChanges(AssistiveTechnology $at, array $data, array $oldDef, array $oldAttr, array $oldTrainings): void
    {
        if ($at->wasRecentlyCreated) return;

        if (isset($data['deficiencies'])) {

            $newDef = array_map('intval',$data['deficiencies']);
            sort($oldDef);
            sort($newDef);

            if ($oldDef !== $newDef) {
                $this->logRelationChange($at,'deficiencies',$oldDef,$newDef);
            }
        }

        if (isset($data['attributes'])) {

            $newAttr = array_filter($data['attributes'], fn($v)=>!is_null($v));

            if ($oldAttr != $newAttr) {
                $this->logRelationChange($at,'attributes',$oldAttr,$newAttr);
            }
        }

        if (isset($data['trainings'])) {

            $newTrain = $at->trainings()->pluck('id')->toArray();

            sort($oldTrainings);
            sort($newTrain);

            if ($oldTrainings !== $newTrain) {
                $this->logRelationChange($at,'trainings',$oldTrainings,$newTrain);
            }
        }
    }

    private function runInspection(AssistiveTechnology $at, array $data): void
    {
        $this->inspectionService->createInspectionForModel($at,$data);
    }

    private function loadFreshRelations(AssistiveTechnology $at): AssistiveTechnology
    {
        return $at->fresh([
            'type',
            'resourceStatus',
            'deficiencies',
            'attributeValues',
            'trainings'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Utilitários
    |--------------------------------------------------------------------------
    */

    protected function logRelationChange(AssistiveTechnology $model, string $field, array $old, array $new): void
    {
        AuditLog::create([
            'user_id'=>auth()->id(),
            'action'=>'updated',
            'auditable_type'=>$model->getMorphClass(),
            'auditable_id'=>$model->id,
            'old_values'=>[$field=>$old],
            'new_values'=>[$field=>$new],
            'ip_address'=>request()?->ip(),
            'user_agent'=>request()?->userAgent(),
        ]);
    }

    private function validateStatusChangeWithActiveLoans(AssistiveTechnology $at, array $data): void
    {
        if (!$at->exists) return;
        if (!isset($data['status_id'])) return;

        $hasActiveLoans = $at->loans()->whereNull('return_date')->exists();

        if (!$hasActiveLoans) return;

        if ($at->status_id != $data['status_id']) {
            throw ValidationException::withMessages([
                'status_id'=>'Não é possível alterar o status enquanto houver empréstimos ativos.'
            ]);
        }
    }
}
