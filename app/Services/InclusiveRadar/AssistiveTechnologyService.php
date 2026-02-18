<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\ResourceType;
use Illuminate\Support\Facades\DB;

class AssistiveTechnologyService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected ResourceAttributeValueService $attributeValueService,
        protected LoanService $loanService,
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

    protected function persist(AssistiveTechnology $assistiveTechnology, array $data): AssistiveTechnology
    {
        // 1. Captura estados antes da mudança (Somente o que a TA gerencia diretamente)
        $oldDeficiencies = $assistiveTechnology->exists
            ? $assistiveTechnology->deficiencies()->pluck('deficiencies.id')->toArray()
            : [];

        $oldAttributes = $assistiveTechnology->exists
            ? $assistiveTechnology->attributeValues()->pluck('value', 'attribute_id')->toArray()
            : [];

        // 2. Processamento de estoque via LoanService
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($assistiveTechnology, (int)$data['quantity']);
        }

        $data = $this->loanService->calculateStockForLoan($assistiveTechnology, $data);

        // 3. Salva dados básicos
        $assistiveTechnology->fill($data)->save();

        // 4. Sincroniza relações diretas (Deficiências e Atributos)
        $this->syncRelations($assistiveTechnology, $data);

        // 5. Log manual de mudanças
        if (!$assistiveTechnology->wasRecentlyCreated) {
            // Deficiências
            if (isset($data['deficiencies'])) {
                $newDeficiencies = array_map('intval', $data['deficiencies']);
                sort($oldDeficiencies);
                sort($newDeficiencies);
                if ($oldDeficiencies !== $newDeficiencies) {
                    $this->logRelationChange($assistiveTechnology, 'deficiencies', $oldDeficiencies, $newDeficiencies);
                }
            }

            // Atributos
            if (isset($data['attributes'])) {
                $newAttributes = array_filter($data['attributes'], fn($v) => !is_null($v));
                if ($oldAttributes != $newAttributes) {
                    $this->logRelationChange($assistiveTechnology, 'attributes', $oldAttributes, $newAttributes);
                }
            }
        }

        // 6. Inspeção
        $this->inspectionService->createInspectionForModel($assistiveTechnology, $data);

        return $assistiveTechnology->fresh([
            'type',
            'resourceStatus',
            'deficiencies',
            'attributeValues',
            'trainings',
        ]);
    }

    protected function logRelationChange(AssistiveTechnology $model, string $field, array $old, array $new): void
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

    protected function syncRelations(AssistiveTechnology $assistiveTechnology, array $data): void
    {
        // 1. Deficiências (Sincronização normal)
        if (isset($data['deficiencies'])) {
            $assistiveTechnology->deficiencies()->sync($data['deficiencies']);
        }

        // 2. Atributos dinâmicos (O CORAÇÃO DO PROBLEMA)
        if (isset($data['attributes'])) {
            $type = ResourceType::find($assistiveTechnology->type_id);
            $validAttributeIds = $type ? $type->attributes()->pluck('type_attributes.id')->toArray() : [];

            // LIMPEZA ATIVA:
            // Identificamos atributos que o usuário deixou em branco ou que não pertencem mais ao tipo
            foreach ($data['attributes'] as $attributeId => $value) {
                if (empty(trim($value))) { // Se estiver vazio ou só com espaços
                    $assistiveTechnology->attributeValues()
                        ->where('attribute_id', $attributeId)
                        ->delete();

                    // Removemos do array para o service de "save" não tentar processar
                    unset($data['attributes'][$attributeId]);
                }
            }

            // Remove atributos que não pertencem mais a este tipo de recurso (caso mudou o Tipo)
            $assistiveTechnology->attributeValues()
                ->whereNotIn('attribute_id', $validAttributeIds)
                ->delete();

            // Salva apenas os que sobraram (que possuem valor real)
            if (!empty($data['attributes'])) {
                $this->attributeValueService->saveValues($assistiveTechnology, $data['attributes']);
            }
        }
    }
}
