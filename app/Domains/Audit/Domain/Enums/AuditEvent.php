<?php

declare(strict_types=1);

namespace App\Domains\Audit\Domain\Enums;

enum AuditEvent: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case RESTORED = 'restored';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Criação',
            self::UPDATED => 'Alteração',
            self::DELETED => 'Exclusão',
            self::RESTORED => 'Restauração',
        };
    }

    /** O verbo da frase em linguagem simples: "Maria alterou o aluno João". */
    public function verb(): string
    {
        return match ($this) {
            self::CREATED => 'criou',
            self::UPDATED => 'alterou',
            self::DELETED => 'excluiu',
            self::RESTORED => 'restaurou',
        };
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $event): array => ['value' => $event->value, 'label' => $event->label()],
            self::cases(),
        );
    }
}
