<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Storage;

use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInspection;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

final class InspectionEvidenceStorage
{
    /**
     * @param  array<int, mixed>  $evidences
     * @return list<string>
     */
    public function store(Inspection $inspection, array $evidences): array
    {
        $storedEvidences = $this->storeFiles($inspection, $evidences);

        if ($storedEvidences === []) {
            return [];
        }

        $storedPaths = array_column($storedEvidences, 'path');

        try {
            $inspection->evidences()->createMany($storedEvidences);
        } catch (Throwable $exception) {
            $this->delete($storedPaths);

            throw $exception;
        }

        return $storedPaths;
    }

    /**
     * @param  array<int, mixed>  $evidences
     * @return list<array{path: string, original_name: string, mime_type: string, size: int}>
     */
    public function storeFiles(Inspection $inspection, array $evidences): array
    {
        if ($evidences === []) {
            return [];
        }

        $storedPaths = [];
        $storedEvidences = [];

        try {
            foreach ($evidences as $file) {
                if (! $file instanceof UploadedFile) {
                    throw new InvalidInspection(
                        'Uma das evidências da inspeção é inválida.'
                    );
                }

                $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'evidencia';
                $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
                $fileName = $name.'_'.uniqid().'.'.$extension;

                $path = Storage::disk('public')->putFileAs(
                    "inspections/{$inspection->id}",
                    $file,
                    $fileName,
                );

                if (! is_string($path) || $path === '') {
                    throw new InvalidInspection(
                        'Não foi possível armazenar uma das evidências da inspeção.'
                    );
                }

                $storedPaths[] = $path;

                $storedEvidences[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType() ?: $file->getMimeType() ?: 'application/octet-stream',
                    'size' => $file->getSize() ?? 0,
                ];
            }
        } catch (Throwable $exception) {
            $this->delete($storedPaths);

            throw $exception;
        }

        return $storedEvidences;
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
