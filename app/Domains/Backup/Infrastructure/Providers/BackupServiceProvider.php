<?php

declare(strict_types=1);

namespace App\Domains\Backup\Infrastructure\Providers;

use App\Domains\Backup\Application\Actions\PruneBackupsAction;
use App\Domains\Backup\Application\Contracts\BackupArchiveStorageContract;
use App\Domains\Backup\Application\Contracts\PruneBackupsActionContract;
use App\Domains\Backup\Infrastructure\Storage\BackupArchiveStorage;
use App\Domains\Backup\UI\Console\Commands\BackupAutomatic;
use Illuminate\Support\ServiceProvider;

final class BackupServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BackupArchiveStorageContract::class, BackupArchiveStorage::class);
        $this->app->bind(PruneBackupsActionContract::class, PruneBackupsAction::class);
    }

    public function boot(): void
    {
        $this->commands([
            BackupAutomatic::class,
        ]);
    }
}
