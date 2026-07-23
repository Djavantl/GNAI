<?php

declare(strict_types=1);

namespace App\Domains\Backup\Tests\Feature;

use App\Domains\Backup\Application\Actions\DeleteBackupAction;
use App\Domains\Backup\Domain\DTOs\CreateBackupDTO;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Backup\Infrastructure\Storage\BackupArchiveStorageContract;
use App\Domains\Backup\Tests\Fakes\FakeBackupArchiveStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DeleteBackupActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_backup_record_and_archive(): void
    {
        $backup = Backup::register(new CreateBackupDTO(
            fileName: 'backup.zip',
            filePath: 'GNAIbackups/backup.zip',
            size: '1 MB',
            status: BackupStatus::SUCCESS,
        ));
        $backup->save();

        $storage = new FakeBackupArchiveStorage();
        $storage->existingArchives = ['GNAIbackups/backup.zip' => true];

        $this->app->instance(BackupArchiveStorageContract::class, $storage);

        app(DeleteBackupAction::class)->execute($backup);

        $this->assertDatabaseMissing('backups', [
            'id' => $backup->id,
        ]);
        self::assertSame(['GNAIbackups/backup.zip'], $storage->deletedArchives);
    }
}
