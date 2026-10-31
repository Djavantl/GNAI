<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Inspections;

use App\Domains\Inclusion\Application\Data\Inspections\CreateInspectionData;
use App\Domains\Inclusion\Domain\DTOs\Inspections\CreateInspectionDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidInspection;
use App\Domains\Inclusion\Domain\Models\Inspection;
use Illuminate\Database\Eloquent\Model;

final readonly class CreateInspectionAction
{
    /**
     * @throws InvalidInspection
     */
    public function execute(
        Model $inspectable,
        CreateInspectionData $data,
        int $registeredBy,
        ?string $state = null,
        ?string $barrierStatus = null,
        ?string $defaultDescription = null,
    ): Inspection {
        if (! $inspectable->exists) {
            throw new InvalidInspection(
                'A inspeção só pode ser vinculada a um registro persistido.'
            );
        }

        $inspectionDTO = new CreateInspectionDTO(
            date: $data->date,
            type: $data->type,
            registeredBy: $registeredBy,
            description: $data->description ?? $defaultDescription,
            state: $state,
            status: $barrierStatus,
        );

        $inspection = Inspection::register($inspectionDTO);

        $inspection->inspectable()->associate($inspectable);
        $inspection->save();

        return $inspection;
    }
}
