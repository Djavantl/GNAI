<?php

declare(strict_types=1);

namespace App\Domains\Audit\UI\Presenters;

use App\Domains\Audit\Domain\Enums\FieldFormat;
use App\Domains\Audit\Domain\Models\AuditRecord;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\AuditableEntities;

/**
 * As duas visões de um registro: `plain` (frase e campos em linguagem simples) e `technical` (o que foi
 * gravado, sem formatação). O React alterna entre elas sem nova requisição.
 */
final class AuditRecordPresenter
{
    public function __construct(private readonly AuditableEntities $entities) {}

    /** @return array<string, mixed> */
    public function present(AuditRecord $record): array
    {
        $fieldsOfType = $this->entities->fieldsOf($record->entity_type);
        $fields = [];

        foreach ($record->changes as $field => $change) {
            $fields[] = $this->readableField($record, $field, $fieldsOfType[$field] ?? null, $change);
        }

        return [
            'id' => $record->id,
            'date' => $record->created_at?->toIso8601String(),
            'event' => $record->event->value,
            'plain' => [
                'summary' => "{$record->author()} {$record->event->verb()} {$record->entity_description}",
                'author' => $record->author(),
                'type' => $this->entities->labelOf($record->entity_type),
                'event' => $record->event->label(),
                'fields' => $fields,
            ],
            'technical' => [
                'id' => $record->id,
                'operation' => $record->operation,
                'event' => $record->event->value,
                'entity_type' => $record->entity_type,
                'entity_id' => $record->entity_id,
                'entity_description' => $record->entity_description,
                'actor_id' => $record->actor_id,
                'actor_name' => $record->actor_name,
                'impersonator_id' => $record->impersonator_id,
                'impersonator_name' => $record->impersonator_name,
                'changes' => $record->changes,
                'referenced_names' => $record->referenced_names,
                'context' => $record->context,
                'created_at' => $record->created_at?->toIso8601String(),
            ],
        ];
    }

    /**
     * Um campo em linguagem simples. Campo que saiu do código aparece com o nome técnico e o valor gravado.
     *
     * @param  array{before?: mixed, after?: mixed}  $change
     * @return array{field: string, label: string, format: string, before: string|null, after: string|null, added: list<string>|null, removed: list<string>|null}
     */
    private function readableField(AuditRecord $record, string $field, ?AuditedField $definition, array $change): array
    {
        $definition ??= AuditedField::text($field);
        $before = $change['before'] ?? null;
        $after = $change['after'] ?? null;
        $names = $record->referenced_names[$field] ?? [];

        $isList = $definition->format === FieldFormat::LIST;
        $items = $names['items'] ?? [];
        $readable = fn (array $values): array => array_values(array_map(
            fn (mixed $value): string => isset($items[(string) $value])
                ? (string) $items[(string) $value]
                : ($definition->referencedModel !== null ? "#{$value}" : $definition->valueLabel($value)),
            $values,
        ));

        return [
            'field' => $field,
            'label' => $definition->label,
            'format' => $definition->format->value,
            'before' => $isList ? null : $definition->format($before, $names['before'] ?? null),
            'after' => $isList ? null : $definition->format($after, $names['after'] ?? null),
            // Lista: o que entrou e o que saiu, no lugar de duas listas inteiras.
            'added' => $isList ? $readable(array_diff((array) $after, (array) $before)) : null,
            'removed' => $isList ? $readable(array_diff((array) $before, (array) $after)) : null,
        ];
    }
}
