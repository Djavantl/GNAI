<?php

declare(strict_types=1);

namespace App\Domains\Auth\Infrastructure\Storage;

use App\Domains\Auth\Domain\Exceptions\InvalidProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final readonly class ProfilePhotoStorage
{
    /**
     * @throws InvalidProfile
     */
    public function store(UploadedFile $photo): string
    {
        $path = $photo->store('profile_photos', 'public');

        if (! is_string($path) || $path === '') {
            throw new InvalidProfile('Não foi possível armazenar a foto do perfil.');
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
