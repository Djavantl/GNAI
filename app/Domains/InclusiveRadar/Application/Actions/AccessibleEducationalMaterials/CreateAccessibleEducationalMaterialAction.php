<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Application\Actions\Inspections\CreateInspectionAction;
use App\Domains\InclusiveRadar\Application\Actions\Inspections\AttachInspectionImagesAction;
use App\Domains\InclusiveRadar\Application\Data\AccessibleEducationalMaterials\CreateAccessibleEducationalMaterialData;
use App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials\AccessibleEducationalMaterialAssetCodeExistsQuery;
use App\Domains\InclusiveRadar\Domain\DTOs\AccessibleEducationalMaterials\CreateAccessibleEducationalMaterialDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\AssetCodeAlreadyInUse;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateAccessibleEducationalMaterialAction
{
    public function __construct(
        private CreateInspectionAction $createInspection,
        private AttachInspectionImagesAction $attachInspectionImages,
        private AccessibleEducationalMaterialAssetCodeExistsQuery $assetCodeExists,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(CreateAccessibleEducationalMaterialData $data, int $registeredBy): AccessibleEducationalMaterial
    {
        $inspection = null;

        $material = DB::transaction(function () use ($data, $registeredBy, &$inspection) {
            $assetCode = AssetCode::optional($data->assetCode);

            if (
                $assetCode !== null
                && $this->assetCodeExists->execute($assetCode)
            ) {
                throw new AssetCodeAlreadyInUse;
            }

            $materialDTO = new CreateAccessibleEducationalMaterialDTO(
                name: $data->name,
                isDigital: $data->isDigital,
                isLoanable: $data->isLoanable,
                quantity: $data->quantity,
                assetCode: $assetCode,
                conservationState: $data->conservationState,
                status: $data->status,
                notes: $data->notes,
                isActive: $data->isActive,
            );

            $material = AccessibleEducationalMaterial::register($materialDTO);

            try {
                $material->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new AssetCodeAlreadyInUse($exception);
            }

            $material->assignTargetAudience($data->deficiencies);
            $material->assignAccessibilityFeatures($data->accessibilityFeatures);

            $inspection = $this->createInspection->execute(
                inspectable: $material,
                data: $data->inspection,
                registeredBy: $registeredBy,
                state: $data->conservationState->value,
                defaultDescription: 'Vistoria inicial de entrada.',
            );

            return $material;
        });

        if ($inspection instanceof Inspection) {
            $this->attachInspectionImages->execute(
                inspection: $inspection,
                images: $data->inspection->images,
            );
        }

        return $material->fresh([
            'deficiencies',
            'accessibilityFeatures',
            'inspections.images',
        ]);
    }
}
