<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Application\Actions\Inspections\CreateInspectionAction;
use App\Domains\InclusiveRadar\Application\Data\AssistiveTechnologies\CreateAssistiveTechnologyData;
use App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies\AssetCodeExistsQuery;
use App\Domains\InclusiveRadar\Domain\DTOs\AssistiveTechnologies\CreateAssistiveTechnologyDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\AssetCodeAlreadyInUse;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
use App\Domains\InclusiveRadar\Infrastructure\Storage\InspectionImageStorage;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateAssistiveTechnologyAction
{
    public function __construct(
        private CreateInspectionAction $createInspection,
        private InspectionImageStorage $inspectionImageStorage,
        private AssetCodeExistsQuery $assetCodeExists,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(CreateAssistiveTechnologyData $data, int $registeredBy): AssistiveTechnology
    {
        $inspection = null;

        try {
            return DB::transaction(function () use ($data, $registeredBy, &$inspection) {
                $assetCode = AssetCode::optional($data->assetCode);

                if (
                    $assetCode !== null
                    && $this->assetCodeExists->execute($assetCode)
                ) {
                    throw new AssetCodeAlreadyInUse;
                }

                $technologyDTO = new CreateAssistiveTechnologyDTO(
                    name: $data->name,
                    digital: $data->isDigital,
                    loanable: $data->isLoanable,
                    quantity: $data->quantity,
                    assetCode: $assetCode,
                    conservationState: $data->conservationState,
                    status: $data->status,
                    notes: $data->notes,
                    active: $data->isActive,
                );

                $technology = AssistiveTechnology::register($technologyDTO);

                try {
                    $technology->save();
                } catch (UniqueConstraintViolationException $exception) {
                    throw new AssetCodeAlreadyInUse($exception);
                }

                $technology->assignTargetAudience($data->deficiencies);

                $inspection = $this->createInspection->execute(
                    inspectable: $technology,
                    data: $data->inspection,
                    registeredBy: $registeredBy,
                    state: $data->conservationState->value,
                    defaultDescription: 'Vistoria inicial de entrada.',
                );

                return $technology->fresh([
                    'deficiencies',
                    'inspections.images',
                ]);
            });
        } catch (Throwable $exception) {
            if ($inspection instanceof Inspection) {
                $this->inspectionImageStorage->deleteDirectory($inspection);
            }

            throw $exception;
        }
    }
}
