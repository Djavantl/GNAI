<?php

namespace App\Models\Traits;

use App\Models\AuditLog;

/**
 * Trait Auditable
 * * Permite que qualquer Model registre automaticamente alterações (Criação, Atualização e Exclusão)
 * em uma tabela de log de auditoria.
 */
trait Auditable
{
    // O Laravel executa automaticamente métodos que começam com 'boot' seguidos do nome da Trait.
    // Registra os 'Observers' para os eventos do ciclo de vida do Model.

    protected static function bootAuditable(): void
    {
        // Registro de criação: Captura todos os atributos iniciais
        static::created(function ($model) {
            self::writeAudit(
                'created',
                $model,
                [], // Sem valores antigos na criação
                $model->getAttributes()
            );
        });

        // Registro de atualização: Compara o que mudou antes de salvar
        static::updating(function ($model) {
            // Obtém apenas os campos que foram alterados no Model
            $dirty = $model->getDirty();

            // Remove o timestamp de atualização para evitar logs desnecessários
            // toda vez que o registro for tocado sem mudanças reais de dados.
            unset($dirty['updated_at']);

            // Se após remover o 'updated_at' não sobrar nada alterado, interrompe o log
            if (empty($dirty)) {
                return;
            }

            // Mapeia os valores originais apenas para as chaves que foram modificadas
            $oldValues = array_intersect_key($model->getOriginal(), $dirty);

            self::writeAudit('updated', $model, $oldValues, $dirty);
        });

        // Registro de exclusão: Captura o estado do objeto antes de ser removido
        static::deleted(function ($model) {
            self::writeAudit(
                'deleted',
                $model,
                $model->getOriginal(),
                []
            );
        });
    }

    // Centraliza a persistência do log.
    // Tipo de ação (created, updated, deleted)
    // Instância do Model que está sendo auditado
    // Valores antes da alteração
    // Valores após a alteração

    protected static function writeAudit(
        string $action,
               $model,
        array $old,
        array $new
    ): void {
        try {
            AuditLog::create([
                'user_id'        => auth()->id(), // ID do usuário autenticado (se houver)
                'action'         => $action,
                'auditable_type' => $model->getMorphClass(), // Classe do Model (ex: App\Models\User)
                'auditable_id'   => $model->getKey(),        // ID do registro afetado
                'old_values'     => !empty($old) ? $old : null,
                'new_values'     => !empty($new) ? $new : null,
                'ip_address'     => request()?->ip(),        // Endereço IP do cliente
                'user_agent'     => request()?->userAgent(), // Navegador/Dispositivo do cliente
            ]);
        } catch (\Throwable $e) {
            // Falhas no log de auditoria não devem impedir a operação principal (transação)
            // de ser concluída. Apenas registramos o erro no log do sistema.
            logger()->error('Falha crítica ao registrar auditoria: ' . $e->getMessage(), [
                'model' => get_class($model),
                'id'    => $model->getKey()
            ]);
        }
    }
}
