<?php

namespace App\Models\InclusiveRadar;

use App\Domains\InclusiveRadar\Domain\Models\Location as DomainLocation;
use App\Models\Traits\Reportable;
use Database\Factories\InclusiveRadar\LocationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;

/**
 * @deprecated Use App\Domains\InclusiveRadar\Domain\Models\Location.
 */
#[UseFactory(LocationFactory::class)]
class Location extends DomainLocation
{
    use Reportable;

    public static function getReportLabel(): string
    {
        return 'Locais';
    }

    public static function getReportColumns(): array
    {
        return [
            'id',
            'name',
            'type',
            'description',
            'is_active',
            'created_at',
        ];
    }

    public static function getReportColumnLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nome',
            'type' => 'Tipo',
            'description' => 'Descrição',
            'is_active' => 'Ativo',
            'created_at' => 'Data de Cadastro',
        ];
    }

    public static function getReportCollectionRelations(): array
    {
        return ['barriers'];
    }
}
