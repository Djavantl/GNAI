<?php

declare(strict_types=1);

namespace App\Domains\Audit\Domain\ValueObjects;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\Enums\FieldFormat;
use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * O que mudou num registro auditável: campo → {before, after}, com os valores já normalizados. Só entram os
 * campos de `auditedFields()` (lista branca) e só os que de fato mudaram.
 */
final readonly class AuditChanges
{
    /** @param  array<string, array{before: mixed, after: mixed}>  $byField */
    private function __construct(public array $byField) {}

    /** Foto dos campos preenchidos de um registro recém-criado. */
    public static function fromCreation(Model&Auditable $entity): self
    {
        $raw = $entity->getAttributes() + $entity->auditExtraValues();
        $byField = [];

        foreach ($entity::auditedFields() as $field => $definition) {
            $after = $definition->format->normalize($raw[$field] ?? null);

            if ($after !== null) {
                $byField[$field] = ['before' => null, 'after' => $after];
            }
        }

        return new self($byField);
    }

    /** Os campos alterados de um registro que acabou de ser salvo (evento `updated`). */
    public static function fromUpdate(Model&Auditable $entity): self
    {
        $changes = $entity->getChanges();
        $byField = [];

        foreach ($entity::auditedFields() as $field => $definition) {
            if (! array_key_exists($field, $changes)) {
                continue;
            }

            $rawBefore = $entity->getRawOriginal($field);
            $before = $definition->format->normalize($rawBefore);
            $after = $definition->format->normalize($changes[$field]);

            // Sigiloso normaliza para o mesmo marcador dos dois lados: a mudança é a do valor cru.
            $changed = $definition->format === FieldFormat::SECRET
                ? $rawBefore !== $changes[$field]
                : $before !== $after;

            if ($changed) {
                $byField[$field] = ['before' => $before, 'after' => $after];
            }
        }

        return new self($byField);
    }

    /** Foto dos campos preenchidos de um registro que acabou de ser excluído. */
    public static function fromDeletion(Model&Auditable $entity): self
    {
        $raw = $entity->getRawOriginal() + $entity->auditExtraValuesForDeletion();
        $byField = [];

        foreach ($entity::auditedFields() as $field => $definition) {
            $before = $definition->format->normalize($raw[$field] ?? null);

            if ($before !== null) {
                $byField[$field] = ['before' => $before, 'after' => null];
            }
        }

        return new self($byField);
    }

    /** Um campo que muda fora dos eventos do Eloquent (pivôs, escritas em massa). */
    public static function forField(Model&Auditable $entity, string $field, mixed $rawBefore, mixed $rawAfter): self
    {
        $definition = $entity::auditedFields()[$field]
            ?? throw new LogicException("O campo '{$field}' não está em ".$entity::class.'::auditedFields().');

        $before = $definition->format->normalize($rawBefore);
        $after = $definition->format->normalize($rawAfter);

        return new self($before === $after ? [] : [$field => ['before' => $before, 'after' => $after]]);
    }

    public function isEmpty(): bool
    {
        return $this->byField === [];
    }

    /**
     * Nome dos registros apontados pelas chaves estrangeiras e listas de ids alteradas, no momento da mudança:
     * o histórico mostra o nome que valia, mesmo se depois o registro for renomeado ou excluído.
     *
     * @param  array<string, AuditedField>  $fields
     * @return array<string, mixed>
     */
    public function referencedNames(array $fields): array
    {
        $names = [];

        foreach ($this->byField as $field => $change) {
            $definition = $fields[$field];

            if ($definition->referencedModel === null) {
                continue;
            }

            if ($definition->format === FieldFormat::REFERENCE) {
                $names[$field] = [
                    'before' => $definition->referencedName($change['before']),
                    'after' => $definition->referencedName($change['after']),
                ];

                continue;
            }

            if ($definition->format === FieldFormat::LIST) {
                $ids = array_values(array_unique([...(array) $change['before'], ...(array) $change['after']]));
                $names[$field] = ['items' => $definition->referencedNames($ids)];
            }
        }

        return $names;
    }
}
