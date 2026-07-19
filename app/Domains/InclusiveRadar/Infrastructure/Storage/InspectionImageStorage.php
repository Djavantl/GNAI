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
        if ($images === []) {
            return [];
        }

        $manager = new ImageManager(new Driver);
        $storedPaths = [];

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

                $inspection->images()->create([
                    'path' => $path,
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => 'image/webp',
                    'size' => strlen($contents),
                ]);
            }
        } catch (Throwable $exception) {
            $this->delete($storedPaths);

            throw $exception;
        }

        return $storedPaths;
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
