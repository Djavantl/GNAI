<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Application\Actions\Inspections\CreateInspectionAction;
use App\Domains\InclusiveRadar\Application\Actions\Inspections\AttachInspectionImagesAction;
use App\Domains\InclusiveRadar\Application\Data\AssistiveTechnologies\UpdateAssistiveTechnologyData;
use App\Domains\InclusiveRadar\Application\Policies\Inspections\InspectionRegistrationPolicy;
use App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies\AssetCodeExistsQuery;
use App\Domains\InclusiveRadar\Domain\DTOs\AssistiveTechnologies\UpdateAssistiveTechnologyDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\AssetCodeAlreadyInUse;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateAssistiveTechnologyAction
{
    public function __construct(
        private CreateInspectionAction $createInspection,
        private AttachInspectionImagesAction $attachInspectionImages,
        private InspectionRegistrationPolicy $inspectionRegistration,
        private AssetCodeExistsQuery $assetCodeExists,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(
        AssistiveTechnology $technology,
        UpdateAssistiveTechnologyData $data,
        int $registeredBy,
    ): AssistiveTechnology {
        $inspection = null;

        $updatedTechnology = DB::transaction(function () use (
            $technology,
            $data,
            $registeredBy,
            &$inspection,
        ) {
            $lockedTechnology = AssistiveTechnology::query()
                ->lockForUpdate()
                ->findOrFail($technology->getKey());
            $assetCode = AssetCode::optional($data->assetCode);

            if (
                $assetCode !== null
                && $assetCode->value() !== $lockedTechnology->asset_code
                && $this->assetCodeExists->execute(
                    assetCode: $assetCode,
                    ignoreId: (int) $lockedTechnology->getKey(),
                )
            ) {
                throw new AssetCodeAlreadyInUse;
            }

            $openLoans = $lockedTechnology->loans()
                ->whereNull('return_date')
                ->count();

            $technologyDTO = new UpdateAssistiveTechnologyDTO(
                name: $data->name,
                isDigital: $data->isDigital,
                isLoanable: $data->isLoanable,
                quantity: $data->quantity,
                assetCode: $assetCode,
                conservationState: $data->conservationState,
                status: $data->status,
                openLoans: $openLoans,
                notes: $data->notes,
                isActive: $data->isActive,
            );

            $lockedTechnology->revise($technologyDTO);

            try {
                $lockedTechnology->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new AssetCodeAlreadyInUse($exception);
            }

            $lockedTechnology->assignTargetAudience($data->deficiencies);

            if ($this->inspectionRegistration->shouldRegister(
                stateChanged: $lockedTechnology->wasChanged('conservation_state'),
                inspection: $data->inspection,
            )) {
                $inspection = $this->createInspection->execute(
                    inspectable: $lockedTechnology,
                    data: $data->inspection,
                    registeredBy: $registeredBy,
                    state: $data->conservationState->value,
                    defaultDescription: 'Vistoria periódica de atualização.',
                );
            }

            return $lockedTechnology;
        });

        if ($inspection instanceof Inspection) {
            $this->attachInspectionImages->execute(
                inspection: $inspection,
                images: $data->inspection->images,
            );
        }

        return $updatedTechnology->fresh([
            'deficiencies',
            'inspections.images',
        ]);
    }
}
