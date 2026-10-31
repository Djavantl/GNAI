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
 * Persiste capa, galeria e palestrantes de uma publicação. Os arquivos novos
 * são gravados antes da transação e descartados se ela falhar; os arquivos
 * substituídos ou removidos só são apagados depois do commit.
 */
final readonly class SyncPublicationRelationsAction
{
    public function __construct(private PublicationMediaStorage $storage) {}

    /**
     * @param  list<int|string>  $speakerIds
     * @param  list<array{file?: UploadedFile, alt?: string|null}>  $newGallery
     * @param  list<array{id?: int|string, alt?: string|null, remove?: bool|string|null}>  $existingGallery
     *
     * @throws Throwable
     */
    public function execute(
        Publication $publication,
        callable $persist,
        array $speakerIds,
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

            $newGalleryRows = [];

            foreach ($newGallery as $entry) {
                $file = $entry['file'] ?? null;

                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $path = $this->storage->store($file);
                $storedPaths[] = $path;
                $newGalleryRows[] = [
                    'path' => $path,
                    'alt_text' => trim((string) ($entry['alt'] ?? '')),
                ];
            }

            DB::transaction(function () use (
                $publication,
                $persist,
                $speakerIds,
                $newCoverPath,
                $removeCover,
                $newGalleryRows,
                $existingGallery,
                &$obsoletePaths,
            ): void {
                $previousCover = $publication->cover_path;

                if ($newCoverPath !== null) {
                    $publication->cover_path = $newCoverPath;
                } elseif ($removeCover) {
                    $publication->cover_path = null;
                    $publication->cover_alt = null;
                }

                if ($publication->cover_path !== null && blank($publication->cover_alt)) {
                    throw new InvalidPublication('Descreva a imagem de capa no texto alternativo.');
                }

                $persist($publication);

                if ($previousCover !== null && $previousCover !== $publication->cover_path) {
                    $obsoletePaths[] = $previousCover;
                }

                $this->syncExistingGallery($publication, $existingGallery, $obsoletePaths);

                $position = (int) $publication->media()->max('position');

                foreach ($newGalleryRows as $row) {
                    $position++;
                    $publication->media()->create([...$row, 'position' => $position]);
                }

                $this->syncSpeakers($publication, $speakerIds);
            });
        } catch (Throwable $exception) {
            $this->storage->deleteMany($storedPaths);

            throw $exception;
        }

        $this->storage->deleteMany($obsoletePaths);

        return $publication;
    }

    /**
     * @param  list<array{id?: int|string, alt?: string|null, remove?: bool|string|null}>  $entries
     * @param  list<string>  $obsoletePaths
     *
     * @throws InvalidPublication
     */
    private function syncExistingGallery(Publication $publication, array $entries, array &$obsoletePaths): void
    {
        if ($entries === []) {
            return;
        }

        $media = $publication->media()->get()->keyBy('id');

        foreach ($entries as $entry) {
            $item = $media->get((int) ($entry['id'] ?? 0));

            if ($item === null) {
                continue;
            }

            if (filter_var($entry['remove'] ?? false, FILTER_VALIDATE_BOOL)) {
                $obsoletePaths[] = $item->path;
                $item->delete();

                continue;
            }

            $alt = trim((string) ($entry['alt'] ?? ''));

            if ($alt === '') {
                throw new InvalidPublication('Todas as fotos da galeria precisam de texto alternativo.');
            }

            if ($alt !== $item->alt_text) {
                $item->update(['alt_text' => $alt]);
            }
        }
    }

    /**
     * @param  list<int|string>  $speakerIds
     */
    private function syncSpeakers(Publication $publication, array $speakerIds): void
    {
        $sync = [];

        foreach (array_values(array_unique(array_map('intval', $speakerIds))) as $index => $id) {
            if ($id > 0) {
                $sync[$id] = ['position' => $index];
            }
        }

        $publication->speakers()->sync($sync);
    }
}
