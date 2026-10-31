<?php

declare(strict_types=1);

namespace App\Domains\Audit\Infrastructure;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * Tipo gravado no histórico → classe do model. Cada domínio registra os seus no próprio provider, então a
 * auditoria não depende dos outros domínios. Serve à formatação (rótulos dos campos) e ao filtro por tipo.
 */
final class AuditableEntities
{
    /** @var array<string, class-string<Model&Auditable>> */
    private array $classes = [];

    /** @param  class-string<Model&Auditable>  ...$classes */
    public function register(string ...$classes): void
    {
        foreach ($classes as $class) {
            $type = $class::auditType();
            $existing = $this->classes[$type] ?? null;

            if ($existing !== null && $existing !== $class) {
                throw new LogicException("O tipo de auditoria '{$type}' já pertence a {$existing}.");
            }

            $this->classes[$type] = $class;
        }
    }

    /**
     * Definição dos campos de um tipo; tipo que saiu do código não tem nenhuma.
     *
     * @return array<string, AuditedField>
     */
    public function fieldsOf(string $type): array
    {
        return isset($this->classes[$type]) ? $this->classes[$type]::auditedFields() : [];
    }

    /** Nome do tipo para pessoas ('Aluno'); tipo que saiu do código aparece como foi gravado. */
    public function labelOf(string $type): string
    {
        return isset($this->classes[$type]) ? $this->classes[$type]::auditLabel() : $type;
    }

    /** @return array<string, class-string<Model&Auditable>> */
    public function all(): array
    {
        return $this->classes;
    }

    /** @return list<array{value: string, label: string}> em ordem alfabética do rótulo */
    public function options(): array
    {
        $options = array_map(
            fn (string $class, string $type): array => ['value' => $type, 'label' => $class::auditLabel()],
            $this->classes,
            array_keys($this->classes),
        );

        usort($options, fn (array $a, array $b): int => strcoll($a['label'], $b['label']));

        return $options;
    }
}
