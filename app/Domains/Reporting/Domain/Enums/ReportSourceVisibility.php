<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Domain\Enums;

enum ReportSourceVisibility: string
{
    case PRIMARY = 'primary';
    case RELATION_ONLY = 'relation_only';
}
