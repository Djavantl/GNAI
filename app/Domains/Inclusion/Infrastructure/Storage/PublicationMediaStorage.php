<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Infrastructure\Storage;

use App\Domains\Inclusion\Domain\Exceptions\InvalidPublication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class PublicationMediaStorage
{
    /**
     * @throws InvalidPublication
     */
    public function store(UploadedFile $file): string
    {
        $path = $file->store('publications', 'public');

        if (! is_string($path) || $path === '') {
            throw new InvalidPublication('Não foi possível armazenar a imagem da publicação.');
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if (filled($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @param  iterable<string|null>  $paths
     */
    public function deleteMany(iterable $paths): void
    {
        foreach ($paths as $path) {
            $this->delete($path);
        }
    }
}
