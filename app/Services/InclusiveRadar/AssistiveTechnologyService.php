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

    // Ações principais

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

    // Regras internas

    protected function persist(AssistiveTechnology $assistiveTechnology, array $data): AssistiveTechnology
    {
        // 1. Captura dos estados antes da mudança

        // Obtém os IDs das deficiências vinculadas antes da alteração
        $oldDeficiencies = $assistiveTechnology->exists
            ? $assistiveTechnology->deficiencies()->pluck('deficiencies.id')->toArray()
            : [];

        // Obtém os valores dos atributos dinâmicos antes da alteração
        $oldAttributes = $assistiveTechnology->exists
            ? $assistiveTechnology->attributeValues()->pluck('value', 'attribute_id')->toArray()
            : [];

        // 2. Processamento padrão

        // Valida se a quantidade solicitada está disponível em estoque
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($assistiveTechnology, (int)$data['quantity']);
        }

        // Ajusta os dados de estoque considerando regras de negócio de empréstimo
        $data = $this->loanService->calculateStockForLoan($assistiveTechnology, $data);

        // Salva os campos nativos disparando o Trait de auditoria automática
        $assistiveTechnology->fill($data)->save();

        // Atualiza as tabelas de relacionamento e atributos dinâmicos
        $this->syncRelations($assistiveTechnology, $data);

        // 3. Comparação e log manual (apenas se não for criação nova)

        // Executa o log manual de relações apenas em edições de registros existentes
        if (!$assistiveTechnology->wasRecentlyCreated) {

            // Compara e registra mudanças na lista de deficiências
            if (isset($data['deficiencies'])) {
                $newDeficiencies = array_map('intval', $data['deficiencies']);
                sort($oldDeficiencies);
                sort($newDeficiencies);

                if ($oldDeficiencies !== $newDeficiencies) {
                    $this->logRelationChange($assistiveTechnology, 'deficiencies', $oldDeficiencies, $newDeficiencies);
                }
            }

            // Compara e registra mudanças nos valores dos atributos dinâmicos
            if (isset($data['attributes'])) {
                // Limpa valores nulos para garantir uma comparação precisa
                $newAttributes = array_filter($data['attributes'], fn($v) => !is_null($v));

                // Registra log se houver diferença entre valores antigos e novos
                if ($oldAttributes != $newAttributes) {
                    $this->logRelationChange($assistiveTechnology, 'attributes', $oldAttributes, $newAttributes);
                }
            }
        }

        // Gera o registro de inspeção vinculado à tecnologia assistiva
        $this->inspectionService->createInspectionForModel($assistiveTechnology, $data);

        // Retorna o objeto atualizado com seus relacionamentos carregados
        return $assistiveTechnology->fresh([
            'type',
            'resourceStatus',
            'deficiencies',
            'attributeValues'
        ]);
    }

    /**
     * Registra manualmente um log de auditoria para campos de relacionamento
     */
    protected function logRelationChange(AssistiveTechnology $model, string $field, array $old, array $new): void
    {
        // Insere um novo registro de auditoria focado em campos não nativos
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

    protected function syncRelations(
        AssistiveTechnology $assistiveTechnology,
        array $data
    ): void {

        // Sincroniza a tabela pivô de deficiências (Many-to-Many)
        if (isset($data['deficiencies'])) {
            $assistiveTechnology
                ->deficiencies()
                ->sync($data['deficiencies']);
        }

        // Processa e limpa atributos dinâmicos baseados no tipo de recurso
        if (isset($data['attributes'])) {

            $type = ResourceType::find($assistiveTechnology->type_id);

            // Identifica quais IDs de atributos pertencem ao tipo de recurso atual
            $validAttributeIds = $type
                ? $type->attributes()
                    ->pluck('type_attributes.id')
                    ->toArray()
                : [];

            // Remove atributos que não pertencem mais a este tipo de tecnologia
            $assistiveTechnology
                ->attributeValues()
                ->whereNotIn('attribute_id', $validAttributeIds)
                ->delete();

            // Salva ou atualiza os novos valores dos atributos dinâmicos
            $this->attributeValueService
                ->saveValues($assistiveTechnology, $data['attributes']);
        }
    }

}
