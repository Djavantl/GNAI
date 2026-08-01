<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Application\Services;

final class ReportOptionCatalog
{
    /** @return array<string, string> */
    public static function inspectableTypes(): array
    {
        return [
            'assistive_technology' => 'Tecnologia assistiva',
            'accessible_educational_material' => 'Material pedagógico acessível',
            'barrier' => 'Barreira',
        ];
    }

    /** @return array<string, string> */
    public static function loanableTypes(): array
    {
        return array_intersect_key(self::inspectableTypes(), array_flip([
            'assistive_technology',
            'accessible_educational_material',
        ]));
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
