<?php

declare(strict_types=1);

namespace App\Domains\Backup\Tests\Feature;

use App\Domains\Backup\Application\Actions\RestoreBackupAction;
use App\Domains\Backup\Application\Contracts\BackupArchiveStorageContract;
use App\Domains\Backup\Domain\DTOs\CreateBackupDTO;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Backup\Tests\Fakes\FakeBackupArchiveStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RestoreBackupActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_restores_a_successful_backup(): void
    {
        $backup = Backup::register(new CreateBackupDTO(
            fileName: 'backup.zip',
            filePath: 'GNAIbackups/backup.zip',
            size: '1 MB',
            status: BackupStatus::SUCCESS,
        ));
        $backup->save();

        $storage = new FakeBackupArchiveStorage;
        $storage->backupArchiveExists = true;

        $this->app->instance(BackupArchiveStorageContract::class, $storage);

        app(RestoreBackupAction::class)->execute($backup);

        self::assertTrue($storage->restoreWasCalled);
    }
}
