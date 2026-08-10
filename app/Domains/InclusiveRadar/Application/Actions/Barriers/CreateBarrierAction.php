<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Barriers;

use App\Domains\InclusiveRadar\Application\Actions\Inspections\AttachInspectionEvidencesAction;
use App\Domains\InclusiveRadar\Application\Actions\Inspections\CreateInspectionAction;
use App\Domains\InclusiveRadar\Application\Data\Barriers\CreateBarrierData;
use App\Domains\InclusiveRadar\Domain\DTOs\Barriers\CreateBarrierDTO;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateBarrierAction
{
    public function __construct(
        private CreateInspectionAction $createInspection,
        private AttachInspectionEvidencesAction $attachInspectionEvidences,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(CreateBarrierData $data, int $registeredBy): Barrier
    {
        $inspection = null;

        $barrier = DB::transaction(function () use ($data, $registeredBy, &$inspection): Barrier {
            $barrierDTO = new CreateBarrierDTO(
                name: $data->name,
                institutionId: $data->institutionId,
                barrierCategoryId: $data->barrierCategoryId,
                priority: $data->priority,
                identifiedAt: $data->identifiedAt,
                registeredBy: $registeredBy,
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
                status: $data->status,
            );

            $barrier = Barrier::register($barrierDTO);

            $barrier->resolveIfStatusRequires($data->status);
            $barrier->save();
            $barrier->assignAffectedAudiences($data->deficiencies);

            $inspection = $this->createInspection->execute(
                inspectable: $barrier,
                data: $data->inspection,
                registeredBy: $registeredBy,
                barrierStatus: $data->status->value,
                defaultDescription: 'Registro inicial da barreira.',
            );

            return $barrier;
        });

        // Anexamos evidências após o commit para não manter transação de banco aberta durante I/O de arquivo.
        // Se o anexo falhar, a barreira e a inspeção inicial permanecem persistidas e a exceção é propagada.
        if ($inspection instanceof Inspection) {
            $this->attachInspectionEvidences->execute(
                inspection: $inspection,
                evidences: $data->inspection->evidences,
            );
        }

        return $barrier->fresh([
            'category',
            'location',
            'deficiencies',
            'inspections.evidences',
        ]);
    }
}
