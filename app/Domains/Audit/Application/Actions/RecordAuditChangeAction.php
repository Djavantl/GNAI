<?php

declare(strict_types=1);

namespace App\Domains\Audit\Application\Actions;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\Enums\AuditEvent;
use App\Domains\Audit\Domain\Enums\FieldFormat;
use App\Domains\Audit\Domain\Models\AuditRecord;
use App\Domains\Audit\Domain\ValueObjects\AuditChanges;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
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
        $referencedNames = $this->referencedNames($entity, $changes);
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

    /**
     * Nome dos registros apontados pelas chaves estrangeiras e listas de ids alteradas, no momento da mudança:
     * o histórico mostra o nome que valia, mesmo se depois o registro for renomeado ou excluído.
     *
     * @return array<string, mixed>
     */
    private function referencedNames(Model&Auditable $entity, AuditChanges $changes): array
    {
        $fields = $entity::auditedFields();
        $names = [];

        foreach ($changes->byField as $field => $change) {
            $definition = $fields[$field];

            if ($definition->referencedModel === null) {
                continue;
            }

            if ($definition->format === FieldFormat::REFERENCE) {
                $names[$field] = [
                    'before' => $this->nameOf($definition, $change['before']),
                    'after' => $this->nameOf($definition, $change['after']),
                ];

                continue;
            }

            if ($definition->format === FieldFormat::LIST) {
                $ids = array_values(array_unique([...(array) $change['before'], ...(array) $change['after']]));
                $names[$field] = ['items' => $this->namesOf($definition, $ids)];
            }
        }

        return $names;
    }

    private function nameOf(AuditedField $definition, mixed $id): ?string
    {
        if ($id === null) {
            return null;
        }

        return $this->namesOf($definition, [$id])[(string) $id] ?? null;
    }

    /**
     * Nome por coluna própria ('name') ou por relação ('person.name', quando o nome mora em outra tabela).
     *
     * @param  list<int|string>  $ids
     * @return array<string, string>
     */
    private function namesOf(AuditedField $definition, array $ids): array
    {
        if ($ids === [] || $definition->nameColumn === null) {
            return [];
        }

        $model = new $definition->referencedModel;
        $key = $model->getKeyName();
        $query = $definition->referencedModel::query()
            ->withoutGlobalScopes()
            ->whereIn($key, $ids);

        if (! str_contains($definition->nameColumn, '.')) {
            return $query
                ->pluck($definition->nameColumn, $key)
                ->filter(fn (mixed $name): bool => $name !== null)
                ->mapWithKeys(fn (mixed $name, mixed $id): array => [(string) $id => (string) $name])
                ->all();
        }

        $relation = substr($definition->nameColumn, 0, (int) strrpos($definition->nameColumn, '.'));

        return $query
            ->with($relation)
            ->get()
            ->mapWithKeys(fn (Model $record): array => [(string) $record->getKey() => data_get($record, $definition->nameColumn)])
            ->filter(fn (mixed $name): bool => $name !== null)
            ->map(fn (mixed $name): string => (string) $name)
            ->all();
    }
}
