<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Application\Services;

final class ReportOptionCatalog
{
    /** @return array<string, string> */
    public static function inspectableTypes(): array
    {
        return [
            'item' => 'Item',
            'barrier' => 'Barreira',
        ];
    }

    /** @return array<string, string> */
    public static function userRoles(): array
    {
        return [
            'admin' => 'Administrador',
            'professional' => 'Profissional',
            'teacher' => 'Professor',
        ];
    }
}
