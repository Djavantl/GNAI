<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Inspections;

use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Infrastructure\Storage\InspectionImageStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class AttachInspectionImagesAction
{
    public function __construct(
        private InspectionImageStorage $imageStorage,
    ) {}

    /**
     * @param  array<int, mixed>  $images
     *
     * @throws Throwable
     */
    public function execute(Inspection $inspection, array $images): void
    {
        if ($images === []) {
            return;
        }

        $storedImages = $this->imageStorage->storeFiles($inspection, $images);
        $storedPaths = array_column($storedImages, 'path');

        try {
            DB::transaction(function () use ($inspection, $storedImages): void {
                $inspection->images()->createMany($storedImages);
            });
        } catch (Throwable $exception) {
            $this->imageStorage->delete($storedPaths);

            throw $exception;
        }
    }
}
