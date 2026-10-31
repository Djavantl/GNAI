<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Infrastructure\Storage;

use App\Domains\Inclusion\Domain\Exceptions\InvalidInstitutionalEvent;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class InstitutionalEventMediaStorage
{
    private const DIRECTORY = 'publications';

    /**
     * @throws InvalidInstitutionalEvent
     */
    public function store(UploadedFile $file): string
    {
        $path = $file->store(self::DIRECTORY, 'public');

        if (! is_string($path) || $path === '') {
            throw new InvalidInstitutionalEvent('Não foi possível armazenar a imagem da publicação.');
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
