<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Inspections;

use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Infrastructure\Storage\InspectionEvidenceStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class AttachInspectionEvidencesAction
{
    public function __construct(
        private InspectionEvidenceStorage $evidenceStorage,
    ) {}

    /**
     * @param  array<int, mixed>  $evidences
     *
     * @throws Throwable
     */
    public function execute(Inspection $inspection, array $evidences): void
    {
        if ($evidences === []) {
            return;
        }

        $storedEvidences = $this->evidenceStorage->storeFiles($inspection, $evidences);
        $storedPaths = array_column($storedEvidences, 'path');

        try {
            DB::transaction(function () use ($inspection, $storedEvidences): void {
                $inspection->evidences()->createMany($storedEvidences);
            });
        } catch (Throwable $exception) {
            $this->evidenceStorage->delete($storedPaths);

            throw $exception;
        }
    }
}
