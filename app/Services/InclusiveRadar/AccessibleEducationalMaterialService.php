<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\ResourceStatus;
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
    | OPERAÇÕES CRUD
    |--------------------------------------------------------------------------
    | Métodos básicos de manipulação do ciclo de vida do Material Pedagógico.
    */

    /**
     * CRIAÇÃO DE NOVO MATERIAL (MPA)
     * * IMPORTÂNCIA: Registra novos materiais didáticos (Braille, kits sensoriais, etc).
     * * FLUXO: Protegido por transação para garantir que o material e suas relações
     * (características, deficiências) sejam criados de forma atômica.
     */
    public function store(array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(
            fn() => $this->persist(new AccessibleEducationalMaterial(), $data)
        );
    }

    /**
     * ATUALIZAÇÃO DE MATERIAL EXISTENTE
     * * IMPORTÂNCIA: Atualiza informações pedagógicas ou quantidades em estoque.
     * * FLUXO: Passa obrigatoriamente pelo método persist para validar integridade de estoque.
     */
    public function update(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(
            fn() => $this->persist($material, $data)
        );
    }

    /**
     * ALTERAÇÃO DE DISPONIBILIDADE
     * * IMPORTÂNCIA: Permite suspender a oferta de um material sem removê-lo do histórico.
     */
    public function toggleActive(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($material) {
            $material->update([
                'is_active' => !$material->is_active
            ]);

            return $material;
        });
    }

    /**
     * EXCLUSÃO DEFINITIVA
     * * IMPORTÂNCIA: Remove o registro físico do material.
     * * SEGURANÇA: Impede a exclusão se houver empréstimos ativos, evitando que materiais
     * que estão com alunos "desapareçam" do controle administrativo.
     */
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
    | FLUXO PRINCIPAL DE PERSISTÊNCIA (PERSIST)
    |--------------------------------------------------------------------------
    | Centraliza a lógica de salvamento para garantir consistência em Store e Update.
    */

    /**
     * MOTOR DE PERSISTÊNCIA INTEGRADO
     * * IMPORTÂNCIA: Orquestra todas as camadas do MPA.
     * * FLUXO: Snapshot do estado -> Validação de Estoque -> Salvar Modelo ->
     * Sincronizar Relações N:N -> Auditoria -> Vistoria Técnica.
     */
    protected function persist(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        [$oldDef, $oldFeatures, $oldAttr, $oldTrainings] = $this->captureOriginalState($material);

        $data = $this->processStock($material, $data);

        $this->saveModel($material, $data);

        $this->syncRelations($material, $data);

        $this->logRelationChanges($material, $data, $oldDef, $oldFeatures, $oldAttr, $oldTrainings);

        $this->runInspection($material, $data);

        return $this->loadFreshRelations($material);
    }

    /*
    |--------------------------------------------------------------------------
    | ETAPAS DO PROCESSO DE SALVAMENTO
    |--------------------------------------------------------------------------
    */

    /**
     * CAPTURA DE ESTADO ORIGINAL
     * * IMPORTÂNCIA: Essencial para o log de auditoria comparativo.
     * * DIFERENCIAL MPA: Além de deficiências, captura as 'accessibilityFeatures'
     * (recursos de acessibilidade específicos do material).
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

    /**
     * ORQUESTRAÇÃO DE ESTOQUE PARA MATERIAL PEDAGÓGICO ACESSÍVEL
     * * IMPORTÂNCIA: Crucial para materiais de consumo ou rotatividade (livros em Braille, kits sensoriais).
     * * FLUXO: Garante que o núcleo de regras (LoanService) valide se a nova quantidade suporta
     * os empréstimos atuais antes de recalcular o saldo disponível.
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

    /**
     * SALVAMENTO DO MODELO BASE
     * * IMPORTÂNCIA: Persiste os dados na tabela de materiais.
     * * REGRA: Garante o status 'available' (disponível) em novos registros caso não informado.
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

    /**
     * SINCRONIZAÇÃO DE RELACIONAMENTOS E ARQUIVOS
     * * IMPORTÂNCIA: Gerencia a complexidade de múltiplos vínculos pedagógicos.
     * * LOGICA: Atualiza deficiências, recursos de acessibilidade, atributos técnicos
     * filtrados por tipo e reconstrói o catálogo de treinamentos/arquivos anexos.
     */
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
            $validAttributeIds = $type ? $type->attributes()->pluck('type_attributes.id')->toArray() : [];

            foreach ($data['attributes'] as $attributeId => $value) {
                if (empty(trim($value))) {
                    $material->attributeValues()->where('attribute_id', $attributeId)->delete();
                    unset($data['attributes'][$attributeId]);
                }
            }

            $material->attributeValues()->whereNotIn('attribute_id', $validAttributeIds)->delete();

            if (!empty($data['attributes'])) {
                $this->attributeValueService->saveValues($material, $data['attributes']);
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

    /**
     * REGISTRO DE VISTORIA (INSPECTION)
     * * IMPORTÂNCIA: Documenta o estado pedagógico e físico do material para conformidade.
     */
    private function runInspection(AccessibleEducationalMaterial $material, array $data): void
    {
        $this->inspectionService->createInspectionForModel($material, $data);
    }

    /**
     * CARREGAMENTO DE RELAÇÕES ATUALIZADAS
     * * IMPORTÂNCIA: Retorna o objeto completo para a interface, sem necessidade de novas queries.
     */
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
    | AUDITORIA E UTILITÁRIOS
    |--------------------------------------------------------------------------
    */

    /**
     * DETECÇÃO DE ALTERAÇÕES EM RELAÇÕES
     * * IMPORTÂNCIA: Monitora mudanças em deficiências, recursos de acessibilidade e
     * atributos para manter o histórico de auditoria preciso.
     */
    private function logRelationChanges(AccessibleEducationalMaterial $material, array $data, array $oldDef, array $oldFeatures, array $oldAttr, array $oldTrainings): void
    {
        if ($material->wasRecentlyCreated) return;

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

    /**
     * VALIDADOR DE MUDANÇA PARA AUDITORIA
     * * IMPORTÂNCIA: Compara arrays de IDs e dispara o log apenas se houver diferença real.
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

    /**
     * PERSISTÊNCIA DO LOG DE AUDITORIA
     * * IMPORTÂNCIA: Mantém a rastreabilidade de quem alterou o material e o que mudou.
     */
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
