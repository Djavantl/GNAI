<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Storage;

use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidTeacher;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class TeacherPhotoStorage
{
    /**
     * @throws InvalidTeacher
     */
    public function store(UploadedFile $photo): string
    {
        $path = $photo->store('photos', 'public');

        if (! is_string($path) || $path === '') {
            throw new InvalidTeacher('Não foi possível armazenar a foto do professor.');
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
