<?php

declare(strict_types=1);

namespace App\Domains\Backup\Tests\Feature;

use App\Domains\Backup\Application\Actions\SyncBackupsAction;
use App\Domains\Backup\Domain\DTOs\CreateBackupDTO;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Backup\Infrastructure\Storage\BackupArchiveMetadata;
use App\Domains\Backup\Infrastructure\Storage\BackupArchiveStorageContract;
use App\Domains\Backup\Tests\Fakes\FakeBackupArchiveStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SyncBackupsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_disk_archives_and_removes_database_orphans(): void
    {
        $orphan = Backup::register(new CreateBackupDTO(
            fileName: 'missing.zip',
            filePath: 'GNAIbackups/missing.zip',
            size: '1 MB',
            status: BackupStatus::SUCCESS,
        ));
        $orphan->save();

        $storage = new FakeBackupArchiveStorage();
        $storage->storedArchives = [
            new BackupArchiveMetadata(
                fileName: 'disk.zip',
                filePath: 'GNAIbackups/disk.zip',
                size: '2 MB',
            ),
        ];
        $storage->existingArchives = [
            'GNAIbackups/disk.zip' => true,
            'GNAIbackups/missing.zip' => false,
        ];

        $this->app->instance(BackupArchiveStorageContract::class, $storage);

        $result = app(SyncBackupsAction::class)->execute();

        self::assertSame(['success' => true, 'created' => 1, 'removed' => 1, 'pruned' => 0], $result);
        $this->assertDatabaseHas('backups', [
            'file_name' => 'disk.zip',
            'file_path' => 'GNAIbackups/disk.zip',
            'user_id' => null,
        ]);
        $this->assertDatabaseMissing('backups', [
            'id' => $orphan->id,
        ]);
    }
}
