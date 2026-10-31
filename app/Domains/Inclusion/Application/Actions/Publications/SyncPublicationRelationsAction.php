<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Publications;

use App\Domains\Inclusion\Domain\Exceptions\InvalidPublication;
use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\Infrastructure\Storage\PublicationMediaStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Salva a publicação com capa, galeria, participantes e etiquetas. Os arquivos novos
 * são gravados antes da transação e descartados se ela falhar; os arquivos
 * substituídos ou removidos só são apagados depois do commit.
 */
final readonly class SyncPublicationRelationsAction
{
    public function __construct(private PublicationMediaStorage $storage) {}

    /**
     * @param  list<array{id?: int|string|null, name?: string|null, participation?: string|null}>  $speakers
     * @param  list<int|string>  $tagIds
     * @param  list<array{file?: UploadedFile, alt?: string|null}>  $newGallery
     * @param  list<array{id?: int|string, alt?: string|null, remove?: bool|string|null}>  $existingGallery
     *
     * @throws InvalidPublication
     * @throws Throwable
     */
    public function execute(
        Publication $publication,
        array $speakers,
        array $tagIds,
        ?UploadedFile $cover,
        bool $removeCover,
        array $newGallery,
        array $existingGallery = [],
    ): Publication {
        $storedPaths = [];
        $obsoletePaths = [];

        try {
            $newCoverPath = $cover ? $this->storage->store($cover) : null;

            if ($newCoverPath) {
                $storedPaths[] = $newCoverPath;
            }

            $newPhotos = [];

            foreach ($newGallery as $entry) {
                $file = $entry['file'] ?? null;

                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $path = $this->storage->store($file);
                $storedPaths[] = $path;
                $newPhotos[] = [
                    'path' => $path,
                    'alt_text' => trim((string) ($entry['alt'] ?? '')),
                ];
            }

            DB::transaction(function () use (
                $publication,
                $speakers,
                $tagIds,
                $newCoverPath,
                $removeCover,
                $newPhotos,
                $existingGallery,
                &$obsoletePaths,
            ): void {
                $previousCover = $publication->replaceCover($newCoverPath, $removeCover);
                $publication->save();

                if ($previousCover !== null) {
                    $obsoletePaths[] = $previousCover;
                }

                $obsoletePaths = [...$obsoletePaths, ...$publication->reviseGallery($existingGallery)];

                $publication->appendGallery($newPhotos);
                $publication->syncSpeakers($speakers);
                $publication->syncTags($tagIds);
            });
        } catch (Throwable $exception) {
            $this->storage->deleteMany($storedPaths);

            throw $exception;
        }

        $this->storage->deleteMany($obsoletePaths);

        return $publication;
    }
}
