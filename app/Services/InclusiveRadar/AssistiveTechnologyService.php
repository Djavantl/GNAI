<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\ResourceStatus;
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
    | OPERAÇÕES CRUD
    |--------------------------------------------------------------------------
    | Métodos básicos de manipulação do ciclo de vida do modelo.
    */

    /**
     * CRIAÇÃO DE NOVO RECURSO (TA)
     * * IMPORTÂNCIA: Inicia o registro de uma Tecnologia Assistiva no sistema.
     * * FLUXO: Encapsulado em transação para garantir que dados de estoque,
     * inspeções e relações sejam criados simultaneamente.
     */
    public function store(array $data): AssistiveTechnology
    {
        return DB::transaction(
            fn() => $this->persist(new AssistiveTechnology(), $data)
        );
    }

    /**
     * ATUALIZAÇÃO DE RECURSO EXISTENTE
     * * IMPORTÂNCIA: Permite a modificação de dados técnicos e de inventário.
     * * FLUXO: O método persist() cuidará das travas de estoque e log de auditoria.
     */
    public function update(AssistiveTechnology $assistiveTechnology, array $data): AssistiveTechnology
    {
        return DB::transaction(
            fn() => $this->persist($assistiveTechnology, $data)
        );
    }

    /**
     * ALTERAÇÃO DE DISPONIBILIDADE (ATIVO/INATIVO)
     * * IMPORTÂNCIA: Permite desativar um recurso sem excluí-lo, preservando o histórico.
     */
    public function toggleActive(AssistiveTechnology $assistiveTechnology): AssistiveTechnology
    {
        return DB::transaction(function () use ($assistiveTechnology) {
            $assistiveTechnology->update([
                'is_active' => !$assistiveTechnology->is_active
            ]);

            return $assistiveTechnology;
        });
    }

    /**
     * EXCLUSÃO DEFINITIVA
     * * IMPORTÂNCIA: Remove o registro do banco de dados.
     * * TRAVA DE SEGURANÇA: Lança uma exceção se houver algum empréstimo em aberto,
     * impedindo a perda de rastreabilidade do patrimônio que está na rua.
     */
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
    | FLUXO PRINCIPAL DE PERSISTÊNCIA (PERSIST)
    |--------------------------------------------------------------------------
    | Centraliza a lógica complexa de salvamento para Store e Update.
    */

    /**
     * MOTOR DE PERSISTÊNCIA INTEGRADO
     * * IMPORTÂNCIA: Garante que toda a "cascata" de salvamento (Estoque -> Modelo ->
     * Relações -> Auditoria -> Inspeção) ocorra em uma ordem lógica e segura.
     */
    protected function persist(AssistiveTechnology $at, array $data): AssistiveTechnology
    {
        // 1. Snapshot do estado atual para auditoria posterior
        [$oldDef, $oldAttr, $oldTrainings] = $this->captureOriginalState($at);

        // 2. Processa travas e cálculos de estoque
        $data = $this->processStock($at, $data);

        // 3. Salva os dados básicos do modelo
        $this->saveModel($at, $data);

        // 4. Sincroniza tabelas relacionadas (deficiências, atributos, treinamentos)
        $this->syncRelations($at, $data);

        // 5. Gera logs de alteração caso não seja uma criação nova
        $this->logRelationChanges($at, $data, $oldDef, $oldAttr, $oldTrainings);

        // 6. Registra a inspeção técnica (Vistoria)
        $this->runInspection($at, $data);

        return $this->loadFreshRelations($at);
    }

    /*
    |--------------------------------------------------------------------------
    | ETAPAS DO PROCESSO DE SALVAMENTO
    |--------------------------------------------------------------------------
    */

    /**
     * CAPTURA DE ESTADO ORIGINAL
     * * IMPORTÂNCIA: Armazena o ID das relações antes de serem alteradas.
     * Essencial para o sistema de auditoria (AuditLog) comparar o Antes e o Depois.
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

    /**
     * ORQUESTRAÇÃO DE ESTOQUE PARA TECNOLOGIA ASSISTIVA
     * * IMPORTÂNCIA: Protege o patrimônio de TA (cadeiras de rodas, lupas eletrônicas, etc).
     * * FLUXO: Primeiro valida se a alteração é permitida e depois recalcula o saldo.
     */
    private function processStock(AssistiveTechnology $at, array $data): array
    {
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($at, (int)$data['quantity']);
        }

        return $this->loanService->calculateStockForLoan($at, $data);
    }

    /**
     * SALVAMENTO DO MODELO BASE
     * * IMPORTÂNCIA: Persiste os dados principais.
     * * REGRA: Se for um novo registro sem status definido, aplica automaticamente o código 'available'.
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

    /**
     * SINCRONIZAÇÃO DE RELACIONAMENTOS COMPLEXOS
     * * IMPORTÂNCIA: Gerencia a integridade das relações N:N e o upload de arquivos.
     * * FLUXO: Sincroniza deficiências atendidas, filtra atributos válidos para o tipo de TA
     * e reconstrói a lista de treinamentos com seus respectivos anexos.
     */
    protected function syncRelations(AssistiveTechnology $at, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $at->deficiencies()->sync($data['deficiencies']);
        }

        if (isset($data['attributes'])) {
            $type = ResourceType::find($at->type_id);
            $valid = $type ? $type->attributes()->pluck('type_attributes.id')->toArray() : [];

            foreach ($data['attributes'] as $id => $value) {
                if (empty(trim($value))) {
                    $at->attributeValues()->where('attribute_id',$id)->delete();
                    unset($data['attributes'][$id]);
                }
            }

            $at->attributeValues()->whereNotIn('attribute_id',$valid)->delete();

            if (!empty($data['attributes'])) {
                $this->attributeValueService->saveValues($at, $data['attributes']);
            }
        }

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

    /**
     * REGISTRO DE VISTORIA (INSPECTION)
     * * IMPORTÂNCIA: Aciona o serviço de inspeção para documentar o estado físico do recurso.
     */
    private function runInspection(AssistiveTechnology $at, array $data): void
    {
        $this->inspectionService->createInspectionForModel($at, $data);
    }

    /**
     * CARREGAMENTO DE RELAÇÕES ATUALIZADAS
     * * IMPORTÂNCIA: Garante que o objeto retornado pelo Service já contenha todos os dados
     * novos (fresh) para exibição imediata na View ou API.
     */
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
    | AUDITORIA E LOGS
    |--------------------------------------------------------------------------
    */

    /**
     * DETECÇÃO E LOG DE ALTERAÇÕES EM RELAÇÕES
     * * IMPORTÂNCIA: Identifica se o usuário mudou deficiências, atributos ou treinamentos
     * e dispara o registro de auditoria apenas se houver diferença real entre o novo e o antigo.
     */
    private function logRelationChanges(AssistiveTechnology $at, array $data, array $oldDef, array $oldAttr, array $oldTrainings): void
    {
        if ($at->wasRecentlyCreated) return;

        if (isset($data['deficiencies'])) {
            $newDef = array_map('intval', $data['deficiencies']);
            sort($oldDef);
            sort($newDef);

            if ($oldDef !== $newDef) {
                $this->logRelationChange($at, 'deficiencies', $oldDef, $newDef);
            }
        }

        if (isset($data['attributes'])) {
            $newAttr = array_filter($data['attributes'], fn($v)=>!is_null($v));
            if ($oldAttr != $newAttr) {
                $this->logRelationChange($at, 'attributes', $oldAttr, $newAttr);
            }
        }

        if (isset($data['trainings'])) {
            $newTrain = $at->trainings()->pluck('id')->toArray();
            sort($oldTrainings);
            sort($newTrain);

            if ($oldTrainings !== $newTrain) {
                $this->logRelationChange($at, 'trainings', $oldTrainings, $newTrain);
            }
        }
    }

    /**
     * PERSISTÊNCIA DO LOG DE AUDITORIA
     * * IMPORTÂNCIA: Salva quem, quando e o que foi alterado para fins de segurança e transparência.
     */
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
}
