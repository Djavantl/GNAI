<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Inspections;

use App\Domains\InclusiveRadar\Application\Data\Inspections\CreateInspectionData;
use App\Domains\InclusiveRadar\Domain\DTOs\Inspections\CreateInspectionDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInspection;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Infrastructure\Storage\InspectionImageStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateInspectionAction
{
    public function __construct(
        private InspectionImageStorage $imageStorage,
    ) {}

    public function execute(
        Model $inspectable,
        CreateInspectionData $data,
        int $registeredBy,
        ?string $state = null,
        ?string $status = null,
        ?string $defaultDescription = null,
    ): Inspection {
        if (! $inspectable->exists) {
            throw new InvalidInspection(
                'A inspeção só pode ser vinculada a um registro persistido.'
            );
        }

        $storedPaths = [];

        try {
            return DB::transaction(function () use (
                $inspectable,
                $data,
                $registeredBy,
                $state,
                $status,
                $defaultDescription,
                &$storedPaths,
            ) {
                $inspectionDTO = new CreateInspectionDTO(
                    date: $data->date,
                    type: $data->type,
                    registeredBy: $registeredBy,
                    description: $data->description ?? $defaultDescription,
                    state: $state,
                    status: $status,
                );

                $inspection = Inspection::register($inspectionDTO);

                $inspection->inspectable()->associate($inspectable);
                $inspection->save();

                $storedPaths = $this->imageStorage->store(
                    $inspection,
                    $data->images,
                );

                return $inspection;
            });
        } catch (Throwable $exception) {
            $this->imageStorage->delete($storedPaths);

            throw $exception;
        }
    }
}
