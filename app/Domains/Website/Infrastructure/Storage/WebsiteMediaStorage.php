<?php

declare(strict_types=1);

namespace App\Domains\Website\Infrastructure\Storage;

use App\Domains\Website\Domain\Exceptions\InvalidWebsite;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class WebsiteMediaStorage
{
    /**
     * @throws InvalidWebsite
     */
    public function store(UploadedFile $file): string
    {
        $path = $file->store('website', 'public');

        if (! is_string($path) || $path === '') {
            throw new InvalidWebsite('Não foi possível armazenar a imagem do site.');
        }

        return $path;
    }

    /**
     * @param  list<array<string, mixed>>  $inputs
     * @param  list<array<string, mixed>>  $previous
     * @return list<array{id: string, image_path: string, alt: string, url: string}>
     *
     * @throws InvalidWebsite
     * @throws Throwable
     */
    public function storeSlideFiles(array $inputs, array $previous): array
    {
        $previousById = array_column($previous, null, 'id');
        $slides = [];
        $storedPaths = [];

        try {
            foreach ($inputs as $input) {
                $id = (string) $input['id'];
                $path = filter_var($input['remove_image'] ?? false, FILTER_VALIDATE_BOOL)
                    ? null : ($previousById[$id]['image_path'] ?? null);

                if (($input['image'] ?? null) instanceof UploadedFile) {
                    $path = $this->store($input['image']);
                    $storedPaths[] = $path;
                }

                if ($path === null) {
                    throw new InvalidWebsite('A imagem do banner não está mais disponível. Selecione o arquivo novamente.');
                }

                $slides[] = [
                    'id' => $id,
                    'image_path' => $path,
                    'alt' => trim((string) ($input['alt'] ?? '')),
                    'url' => trim((string) ($input['url'] ?? '')),
                ];
            }
        } catch (Throwable $exception) {
            $this->deleteMany($storedPaths);

            throw $exception;
        }

        return $slides;
    }

    /**
     * @param  iterable<array<string, mixed>>  $sections
     * @return list<string>
     */
    public function pathsForSections(iterable $sections): array
    {
        $paths = [];

        foreach ($sections as $section) {
            if (! empty($section['image_path'])) {
                $paths[] = $section['image_path'];
            }

            foreach ($section['slides'] ?? [] as $slide) {
                if (! empty($slide['image_path'])) {
                    $paths[] = $slide['image_path'];
                }
            }
        }

        return $paths;
    }

    /**
     * @param  iterable<string|null>  $paths
     */
    public function deleteMany(iterable $paths): void
    {
        foreach ($paths as $path) {
            if (filled($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    public static function url(?string $path): ?string
    {
        return filled($path) ? asset('storage/'.$path) : null;
    }
}
