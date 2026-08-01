<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Domain\Enums;

enum ReportColumnType: string
{
    case TEXT = 'text';
    case DATE = 'date';
    case BOOLEAN = 'boolean';
    case SELECT = 'select';
}
