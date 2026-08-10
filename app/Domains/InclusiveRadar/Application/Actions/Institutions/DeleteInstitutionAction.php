<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Institutions;

use App\Domains\InclusiveRadar\Application\Queries\Institutions\InstitutionHasActiveBarriersQuery;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitution;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteInstitutionAction
{
    public function __construct(
        private InstitutionHasActiveBarriersQuery $hasActiveBarriers,
    ) {}


    /**
     * @throws Throwable
     */
    public function execute(Institution $institution): void
    {
        DB::transaction(function () use ($institution): void {
            $lockedInstitution = Institution::query()
                ->lockForUpdate()
                ->findOrFail($institution->getKey());

            if ($this->hasActiveBarriers->execute($lockedInstitution)) {
                throw new InvalidInstitution(
                    'Não é possível excluir esta instituição pois ela possui barreiras ativas.'
                );
            }

            $lockedInstitution->locations()->delete();
            $lockedInstitution->delete();
        });
    }
}
