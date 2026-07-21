<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Institutions;

use App\Domains\InclusiveRadar\Application\Data\Institutions\UpdateInstitutionData;
use App\Domains\InclusiveRadar\Application\Queries\Institutions\InstitutionHasUnresolvedBarriersQuery;
use App\Domains\InclusiveRadar\Domain\DTOs\Institutions\UpdateInstitutionDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitution;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateInstitutionAction
{
    public function __construct(
        private InstitutionHasUnresolvedBarriersQuery $hasUnresolvedBarriers,
    ) {}

    /**
     * @throws InvalidInstitution
     * @throws Throwable
     */
    public function execute(Institution $institution, UpdateInstitutionData $data): Institution
    {
        return DB::transaction(function () use ($institution, $data): Institution {
            $lockedInstitution = Institution::query()
                ->lockForUpdate()
                ->findOrFail($institution->getKey());

            $willDeactivate = $lockedInstitution->is_active && ! $data->isActive;

            if ($willDeactivate && $this->hasUnresolvedBarriers->execute($lockedInstitution)) {
                throw new InvalidInstitution(
                    'Existem barreiras não resolvidas. Resolva-as antes de desativar a instituição.'
                );
            }

            $institutionDTO = new UpdateInstitutionDTO(
                name: $data->name,
                city: $data->city,
                state: $data->state,
                latitude: $data->latitude,
                longitude: $data->longitude,
                shortName: $data->shortName,
                district: $data->district,
                address: $data->address,
                defaultZoom: $data->defaultZoom,
                active: $data->isActive,
            );

            $lockedInstitution->revise($institutionDTO);
            $lockedInstitution->save();

            if ($willDeactivate) {
                $lockedInstitution->locations()->update([
                    'is_active' => false,
                ]);
            }

            return $lockedInstitution;
        });
    }
}
