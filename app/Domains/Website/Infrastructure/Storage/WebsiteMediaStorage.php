<?php

declare(strict_types=1);

namespace App\Domains\Website\Infrastructure\Storage;

use App\Domains\Website\Domain\Exceptions\InvalidWebsite;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
