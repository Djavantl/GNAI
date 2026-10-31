<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Locations;

use App\Domains\Inclusion\Application\Queries\Locations\LocationHasUnresolvedBarriersQuery;
use App\Domains\Inclusion\Domain\Exceptions\InvalidLocation;
use App\Domains\Inclusion\Domain\Models\Location;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteLocationAction
{
    public function __construct(
        private LocationHasUnresolvedBarriersQuery $hasUnresolvedBarriers,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Location $location): void
    {
        DB::transaction(function () use ($location): void {
            $lockedLocation = Location::query()
                ->lockForUpdate()
                ->findOrFail($location->getKey());

            if ($this->hasUnresolvedBarriers->execute($lockedLocation)) {
                throw new InvalidLocation(
                    'Não é possível excluir este ponto de referência pois ele possui barreiras ativas.'
                );
            }

            $lockedLocation->delete();
        });
    }
}
