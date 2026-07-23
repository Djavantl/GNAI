<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Barriers;

use App\Domains\InclusiveRadar\Application\Actions\Inspections\AttachInspectionImagesAction;
use App\Domains\InclusiveRadar\Application\Actions\Inspections\CreateInspectionAction;
use App\Domains\InclusiveRadar\Application\Data\Barriers\UpdateBarrierData;
use App\Domains\InclusiveRadar\Domain\DTOs\Barriers\UpdateBarrierDTO;
use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateBarrierAction
{
    public function __construct(
        private CreateInspectionAction $createInspection,
        private AttachInspectionImagesAction $attachInspectionImages,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Barrier $barrier, UpdateBarrierData $data, int $registeredBy): Barrier
    {
        $inspection = null;

        $updatedBarrier = DB::transaction(function () use (
            $barrier,
            $data,
            $registeredBy,
            &$inspection,
        ): Barrier {
            $lockedBarrier = Barrier::query()
                ->lockForUpdate()
                ->findOrFail($barrier->getKey());
            $oldStatus = $lockedBarrier->latestStatus();
            $newStatus = $data->status ?? $oldStatus ?? BarrierStatus::IDENTIFIED;
            $statusChanged = $oldStatus !== $newStatus;
            $hasInteraction = filled($data->inspection->description) || $data->inspection->images !== [];

            $barrierDTO = new UpdateBarrierDTO(
                name: $data->name,
                institutionId: $data->institutionId,
                barrierCategoryId: $data->barrierCategoryId,
                priority: $data->priority,
                identifiedAt: $data->identifiedAt,
                description: $data->description,
                locationId: $data->noLocation ? null : $data->locationId,
                latitude: $data->latitude,
                longitude: $data->longitude,
                locationSpecificDetails: $data->locationSpecificDetails,
                affectedStudentId: $data->affectedStudentId,
                affectedProfessionalId: $data->affectedProfessionalId,
                affectedPersonName: $data->affectedPersonName,
                affectedPersonRole: $data->affectedPersonRole,
                isAnonymous: $data->isAnonymous,
                notApplicable: $data->notApplicable,
                isActive: $data->isActive,
                status: $newStatus,
            );

            $lockedBarrier->revise($barrierDTO);
            $lockedBarrier->resolveIfStatusRequires($newStatus);
            $lockedBarrier->save();
            $lockedBarrier->assignAffectedAudiences($data->deficiencies);

            if (! $statusChanged && ! $hasInteraction) {
                return $lockedBarrier;
            }

            $inspection = $this->createInspection->execute(
                inspectable: $lockedBarrier,
                data: $data->inspection,
                registeredBy: $registeredBy,
                barrierStatus: $newStatus->value,
            );

            return $lockedBarrier;
        });

        // Anexamos imagens após o commit para não manter transação de banco aberta durante I/O de arquivo.
        // Se o anexo falhar, a barreira e a nova inspeção permanecem persistidas e a exceção é propagada.
        if ($inspection instanceof Inspection) {
            $this->attachInspectionImages->execute(
                inspection: $inspection,
                images: $data->inspection->images,
            );
        }

        return $updatedBarrier->fresh([
            'category',
            'location',
            'deficiencies',
            'inspections.images',
        ]);
    }
}
