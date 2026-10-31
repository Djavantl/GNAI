<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Items;

use App\Domains\InclusiveRadar\Application\Actions\Inspections\CreateInspectionAction;
use App\Domains\InclusiveRadar\Application\Actions\Inspections\AttachInspectionEvidencesAction;
use App\Domains\InclusiveRadar\Application\Data\Items\UpdateItemData;
use App\Domains\InclusiveRadar\Application\Policies\Inspections\InspectionRegistrationPolicy;
use App\Domains\InclusiveRadar\Application\Queries\Items\ItemAssetCodeExistsQuery;
use App\Domains\InclusiveRadar\Domain\DTOs\Items\UpdateItemDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\AssetCodeAlreadyInUse;
use App\Domains\InclusiveRadar\Domain\Models\Item;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateItemAction
{
    public function __construct(
        private CreateInspectionAction $createInspection,
        private AttachInspectionEvidencesAction $attachInspectionEvidences,
        private InspectionRegistrationPolicy $inspectionRegistration,
        private ItemAssetCodeExistsQuery $assetCodeExists,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(
        Item $item,
        UpdateItemData $data,
        int $registeredBy,
    ): Item {
        $inspection = null;

        $updatedItem = DB::transaction(function () use (
            $item,
            $data,
            $registeredBy,
            &$inspection,
        ) {
            $lockedItem = Item::query()
                ->lockForUpdate()
                ->findOrFail($item->getKey());
            $assetCode = AssetCode::optional($data->assetCode);

            if (
                $assetCode !== null
                && $assetCode->value() !== $lockedItem->asset_code
                && $this->assetCodeExists->execute(
                    assetCode: $assetCode,
                    ignoreId: (int) $lockedItem->getKey(),
                )
            ) {
                throw new AssetCodeAlreadyInUse;
            }

            $openLoans = $lockedItem->loans()
                ->whereNull('return_date')
                ->count();

            $itemDTO = new UpdateItemDTO(
                itemType: $data->itemType,
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

            $lockedItem->revise($itemDTO);

            try {
                $lockedItem->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new AssetCodeAlreadyInUse($exception);
            }

            $lockedItem->assignTargetAudience($data->deficiencies);
            $lockedItem->assignAccessibilityFeatures($data->accessibilityFeatures);

            if ($this->inspectionRegistration->shouldRegister(
                stateChanged: $lockedItem->wasChanged('conservation_state'),
                inspection: $data->inspection,
            )) {
                $inspection = $this->createInspection->execute(
                    inspectable: $lockedItem,
                    data: $data->inspection,
                    registeredBy: $registeredBy,
                    state: $data->conservationState->value,
                    defaultDescription: 'Vistoria periódica de atualização.',
                );
            }

            return $lockedItem;
        });

        if ($inspection instanceof Inspection) {
            $this->attachInspectionEvidences->execute(
                inspection: $inspection,
                evidences: $data->inspection->evidences,
            );
        }

        return $updatedItem->fresh([
            'deficiencies',
            'accessibilityFeatures',
            'inspections.evidences',
        ]);
    }
}
