<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\InstitutionalEvents;

use App\Domains\Inclusion\Domain\Models\InstitutionalEvent;
use App\Domains\Inclusion\Infrastructure\Storage\InstitutionalEventMediaStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteInstitutionalEventAction
{
    public function __construct(private InstitutionalEventMediaStorage $storage) {}

    /**
     * @throws Throwable
     */
    public function execute(InstitutionalEvent $event): void
    {
        $paths = DB::transaction(function () use ($event): array {
            $record = InstitutionalEvent::query()->findOrFail($event->getKey());
            $paths = [$record->cover_path, ...$record->media()->pluck('path')->all()];

            $record->delete();

            return $paths;
        });

        $this->storage->deleteMany($paths);
    }
}
