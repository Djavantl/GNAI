<?php

declare(strict_types=1);

namespace App\Domains\Audit\Application\Actions;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\Enums\AuditEvent;
use App\Domains\Audit\Domain\Models\AuditRecord;
use App\Domains\Audit\Domain\ValueObjects\AuditChanges;
use App\Domains\Audit\Infrastructure\AuditContext;
use Illuminate\Database\Eloquent\Model;

/**
 * Grava uma alteração no histórico. Roda dentro da transação de quem alterou: se ela for desfeita, o registro
 * vai junto. Várias gravações do mesmo registro numa operação viram um registro de auditoria só.
 */
final class RecordAuditChangeAction
{
    public function __construct(private readonly AuditContext $context) {}

    public function execute(Model&Auditable $entity, AuditEvent $event, AuditChanges $changes): void
    {
        if ($changes->isEmpty()) {
            return;
        }

        $entityKey = $entity::auditType().':'.$entity->getKey();
        $referencedNames = $changes->referencedNames($entity::auditedFields());
        $operationRecord = $this->context->operationRecord($entityKey);

        if (
            $operationRecord !== null
            && $event === AuditEvent::UPDATED
            && $operationRecord->event !== AuditEvent::DELETED
        ) {
            $operationRecord->merge($changes, $referencedNames, $entity->auditDescription());

            if ($operationRecord->hasNoChanges()) {
                $operationRecord->delete();
                $this->context->forgetRecord($entityKey);
            } else {
                $operationRecord->save();
            }

            return;
        }

        $actor = $this->context->actor();
        $impersonator = $this->context->impersonator();

        $this->context->rememberRecord($entityKey, AuditRecord::query()->create([
            'operation' => $this->context->operation(),
            'actor_id' => $actor?->id,
            'actor_name' => $actor?->name,
            'impersonator_id' => $impersonator?->id,
            'impersonator_name' => $impersonator?->name,
            'event' => $event,
            'entity_type' => $entity::auditType(),
            'entity_id' => (string) $entity->getKey(),
            'entity_description' => $entity->auditDescription(),
            'changes' => $changes->byField,
            'referenced_names' => $referencedNames ?: null,
            'context' => $this->context->requestData(),
        ]));
    }
}
