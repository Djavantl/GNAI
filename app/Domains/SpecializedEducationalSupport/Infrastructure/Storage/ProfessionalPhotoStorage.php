<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Storage;

use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidProfessional;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class ProfessionalPhotoStorage
{
    /**
     * @throws InvalidProfessional
     */
    public function store(UploadedFile $photo): string
    {
        $path = $photo->store('photos', 'public');

        if (! is_string($path) || $path === '') {
            throw new InvalidProfessional('Não foi possível armazenar a foto do profissional.');
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if (filled($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
