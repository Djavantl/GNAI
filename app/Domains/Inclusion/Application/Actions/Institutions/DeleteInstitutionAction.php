<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Institutions;

use App\Domains\Inclusion\Application\Queries\Institutions\InstitutionHasActiveBarriersQuery;
use App\Domains\Inclusion\Domain\Exceptions\InvalidInstitution;
use App\Domains\Inclusion\Domain\Models\Institution;
use App\Domains\Inclusion\Domain\Models\Location;
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

            // Um a um (e não delete em massa) para cada ponto excluído entrar na auditoria.
            $lockedInstitution->locations()
                ->get()
                ->each(fn (Location $location) => $location->delete());
            $lockedInstitution->delete();
        });
    }
}
