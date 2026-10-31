<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Publications;

use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\Infrastructure\Storage\PublicationMediaStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeletePublicationAction
{
    public function __construct(private PublicationMediaStorage $storage) {}

    /**
     * @throws Throwable
     */
    public function execute(Publication $publication): void
    {
        $paths = DB::transaction(function () use ($publication): array {
            $record = Publication::query()->findOrFail($publication->getKey());
            $paths = [$record->cover_path, ...$record->media()->pluck('path')->all()];

            $record->delete();

            return $paths;
        });

        $this->storage->deleteMany($paths);
    }
}
