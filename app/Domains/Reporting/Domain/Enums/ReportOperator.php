<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Domain\Enums;

enum ReportOperator: string
{
    case EQUALS = 'eq';
    case NOT_EQUALS = 'neq';
    case CONTAINS = 'contains';
    case GREATER_THAN = 'gt';
    case GREATER_THAN_OR_EQUALS = 'gte';
    case LESS_THAN = 'lt';
    case LESS_THAN_OR_EQUALS = 'lte';

    public function sqlOperator(): string
    {
        return match ($this) {
            self::EQUALS => '=',
            self::NOT_EQUALS => '!=',
            self::GREATER_THAN => '>',
            self::GREATER_THAN_OR_EQUALS => '>=',
            self::LESS_THAN => '<',
            self::LESS_THAN_OR_EQUALS => '<=',
            self::CONTAINS => 'like',
        };
    }
}
