<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Storage;

use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInspection;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

final class InspectionImageStorage
{
    /**
     * @param  array<int, mixed>  $images
     * @return list<string>
     */
    public function store(Inspection $inspection, array $images): array
    {
        $storedImages = $this->storeFiles($inspection, $images);

        if ($storedImages === []) {
            return [];
        }

        $storedPaths = array_column($storedImages, 'path');

        try {
            $inspection->images()->createMany($storedImages);
        } catch (Throwable $exception) {
            $this->delete($storedPaths);

            throw $exception;
        }

        return $storedPaths;
    }

    /**
     * @param  array<int, mixed>  $images
     * @return list<array{path: string, original_name: string, mime_type: string, size: int}>
     */
    public function storeFiles(Inspection $inspection, array $images): array
    {
        if ($images === []) {
            return [];
        }

        $manager = new ImageManager(new Driver);
        $storedPaths = [];
        $storedImages = [];

        try {
            foreach ($images as $image) {
                if (! $image instanceof UploadedFile) {
                    throw new InvalidInspection(
                        'Uma das imagens da inspeção é inválida.'
                    );
                }

                $name = pathinfo(
                    $image->getClientOriginalName(),
                    PATHINFO_FILENAME,
                );
                $fileName = $name.'_'.uniqid().'.webp';
                $path = "inspections/{$inspection->id}/{$fileName}";
                $optimizedImage = $manager->read($image)
                    ->scale(width: 600)
                    ->toWebp(60);
                $contents = (string) $optimizedImage;

                $stored = Storage::disk('public')->put($path, $contents);

                if (! $stored) {
                    throw new InvalidInspection(
                        'Não foi possível armazenar uma das imagens da inspeção.'
                    );
                }

                $storedPaths[] = $path;

                $storedImages[] = [
                    'path' => $path,
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => 'image/webp',
                    'size' => strlen($contents),
                ];
            }
        } catch (Throwable $exception) {
            $this->delete($storedPaths);

            throw $exception;
        }

        return $storedImages;
    }

    /**
     * @param  list<string>  $paths
     */
    public function delete(array $paths): void
    {
        if ($paths === []) {
            return;
        }

        try {
            Storage::disk('public')->delete($paths);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function deleteDirectory(Inspection $inspection): void
    {
        if ($inspection->getKey() === null) {
            return;
        }

        try {
            Storage::disk('public')->deleteDirectory(
                "inspections/{$inspection->getKey()}"
            );
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
