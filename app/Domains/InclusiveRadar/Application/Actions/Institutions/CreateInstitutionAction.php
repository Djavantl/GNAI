<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Institutions;

use App\Domains\InclusiveRadar\Application\Data\Institutions\CreateInstitutionData;
use App\Domains\InclusiveRadar\Application\Queries\Institutions\InstitutionExistsQuery;
use App\Domains\InclusiveRadar\Domain\DTOs\Institutions\CreateInstitutionDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitution;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateInstitutionAction
{
    public function __construct(
        private InstitutionExistsQuery $institutionExists,
    ) {}

    /**
     * @throws InvalidInstitution
     * @throws Throwable
     */
    public function execute(CreateInstitutionData $data): Institution
    {
        return DB::transaction(function () use ($data): Institution {
            $institutionDTO = new CreateInstitutionDTO(
                name: $data->name,
                city: $data->city,
                state: $data->state,
                latitude: $data->latitude,
                longitude: $data->longitude,
                shortName: $data->shortName,
                district: $data->district,
                address: $data->address,
                defaultZoom: $data->defaultZoom,
                isActive: $data->isActive,
            );

            $institution = Institution::register($institutionDTO);

            if ($this->institutionExists->execute($data)) {
                throw new InvalidInstitution('Já existe uma instituição cadastrada.');
            }

            $institution->save();

            return $institution;
        });
    }
}
