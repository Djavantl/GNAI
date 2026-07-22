<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Application\Actions\Inspections\CreateInspectionAction;
use App\Domains\InclusiveRadar\Application\Actions\Inspections\AttachInspectionImagesAction;
use App\Domains\InclusiveRadar\Application\Data\AccessibleEducationalMaterials\UpdateAccessibleEducationalMaterialData;
use App\Domains\InclusiveRadar\Application\Policies\Inspections\InspectionRegistrationPolicy;
use App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials\AccessibleEducationalMaterialAssetCodeExistsQuery;
use App\Domains\InclusiveRadar\Domain\DTOs\AccessibleEducationalMaterials\UpdateAccessibleEducationalMaterialDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\AssetCodeAlreadyInUse;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateAccessibleEducationalMaterialAction
{
    public function __construct(
        private CreateInspectionAction $createInspection,
        private AttachInspectionImagesAction $attachInspectionImages,
        private InspectionRegistrationPolicy $inspectionRegistration,
        private AccessibleEducationalMaterialAssetCodeExistsQuery $assetCodeExists,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(
        AccessibleEducationalMaterial $material,
        UpdateAccessibleEducationalMaterialData $data,
        int $registeredBy,
    ): AccessibleEducationalMaterial {
        $inspection = null;

        $updatedMaterial = DB::transaction(function () use (
            $material,
            $data,
            $registeredBy,
            &$inspection,
        ) {
            $lockedMaterial = AccessibleEducationalMaterial::query()
                ->lockForUpdate()
                ->findOrFail($material->getKey());
            $assetCode = AssetCode::optional($data->assetCode);

            if (
                $assetCode !== null
                && $assetCode->value() !== $lockedMaterial->asset_code
                && $this->assetCodeExists->execute(
                    assetCode: $assetCode,
                    ignoreId: (int) $lockedMaterial->getKey(),
                )
            ) {
                throw new AssetCodeAlreadyInUse;
            }

            $openLoans = $lockedMaterial->loans()
                ->whereNull('return_date')
                ->count();

            $materialDTO = new UpdateAccessibleEducationalMaterialDTO(
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

            $lockedMaterial->revise($materialDTO);

            try {
                $lockedMaterial->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new AssetCodeAlreadyInUse($exception);
            }

            $lockedMaterial->assignTargetAudience($data->deficiencies);
            $lockedMaterial->assignAccessibilityFeatures($data->accessibilityFeatures);

            if ($this->inspectionRegistration->shouldRegister(
                stateChanged: $lockedMaterial->wasChanged('conservation_state'),
                inspection: $data->inspection,
            )) {
                $inspection = $this->createInspection->execute(
                    inspectable: $lockedMaterial,
                    data: $data->inspection,
                    registeredBy: $registeredBy,
                    state: $data->conservationState->value,
                    defaultDescription: 'Vistoria periódica de atualização.',
                );
            }

            return $lockedMaterial;
        });

        if ($inspection instanceof Inspection) {
            $this->attachInspectionImages->execute(
                inspection: $inspection,
                images: $data->inspection->images,
            );
        }

        return $updatedMaterial->fresh([
            'deficiencies',
            'accessibilityFeatures',
            'inspections.images',
        ]);
    }
}
