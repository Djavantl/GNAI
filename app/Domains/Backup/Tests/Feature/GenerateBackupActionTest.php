<?php

declare(strict_types=1);

namespace App\Domains\Backup\Tests\Feature;

use App\Domains\Backup\Application\Actions\GenerateBackupAction;
use App\Domains\Backup\Application\Contracts\BackupArchiveStorageContract;
use App\Domains\Backup\Application\Contracts\PruneBackupsActionContract;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Backup\Infrastructure\Storage\BackupArchiveMetadata;
use App\Domains\Backup\Tests\Fakes\FakeBackupArchiveStorage;
use App\Domains\Backup\Tests\Fakes\FakePruneBackupsAction;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

final class GenerateBackupActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_and_registers_a_backup(): void
    {
        $user = User::factory()->create();
        $storage = new FakeBackupArchiveStorage;
        $storage->generatedArchive = new BackupArchiveMetadata(
            fileName: 'generated.zip',
            filePath: 'GNAIbackups/generated.zip',
            size: '2 MB',
        );

        $this->app->instance(BackupArchiveStorageContract::class, $storage);

        $backup = app(GenerateBackupAction::class)->execute($user->id);

        self::assertSame('generated.zip', $backup->file_name);
        self::assertSame(BackupStatus::SUCCESS, $backup->status);
        $this->assertDatabaseHas('backups', [
            'id' => $backup->id,
            'file_name' => 'generated.zip',
            'file_path' => 'GNAIbackups/generated.zip',
            'size' => '2 MB',
            'status' => BackupStatus::SUCCESS->value,
            'user_id' => $user->id,
        ]);
    }

    public function test_it_registers_automatic_backup_without_user(): void
    {
        $storage = new FakeBackupArchiveStorage;
        $storage->generatedArchive = new BackupArchiveMetadata(
            fileName: 'automatic.zip',
            filePath: 'GNAIbackups/automatic.zip',
            size: '3 MB',
        );

        $this->app->instance(BackupArchiveStorageContract::class, $storage);

        $backup = app(GenerateBackupAction::class)->execute();

        $this->assertDatabaseHas('backups', [
            'id' => $backup->id,
            'file_name' => 'automatic.zip',
            'file_path' => 'GNAIbackups/automatic.zip',
            'user_id' => null,
        ]);
    }

    public function test_it_does_not_register_backup_when_archive_generation_fails(): void
    {
        $storage = new FakeBackupArchiveStorage;
        $storage->generateException = new RuntimeException('Falha simulada ao gerar arquivo.');

        $this->app->instance(BackupArchiveStorageContract::class, $storage);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Falha simulada ao gerar arquivo.');

        try {
            app(GenerateBackupAction::class)->execute();
        } finally {
            self::assertSame(0, Backup::query()->count());
        }
    }

    public function test_it_cleans_generated_archive_when_database_registration_fails(): void
    {
        $storage = new FakeBackupArchiveStorage;
        $storage->generatedArchive = new BackupArchiveMetadata(
            fileName: 'orphan.zip',
            filePath: 'GNAIbackups/orphan.zip',
            size: '4 MB',
        );
        $storage->existingArchives = ['GNAIbackups/orphan.zip' => true];

        $this->app->instance(BackupArchiveStorageContract::class, $storage);

        Schema::drop('backups');

        $this->expectException(QueryException::class);

        try {
            app(GenerateBackupAction::class)->execute();
        } finally {
            self::assertSame(['GNAIbackups/orphan.zip'], $storage->deletedArchives);
        }
    }

    public function test_it_returns_created_backup_when_prune_fails(): void
    {
        $storage = new FakeBackupArchiveStorage;
        $storage->generatedArchive = new BackupArchiveMetadata(
            fileName: 'prune-fail.zip',
            filePath: 'GNAIbackups/prune-fail.zip',
            size: '5 MB',
        );

        $prune = new FakePruneBackupsAction;
        $prune->shouldFail = true;

        $this->app->instance(BackupArchiveStorageContract::class, $storage);
        $this->app->instance(PruneBackupsActionContract::class, $prune);

        $backup = app(GenerateBackupAction::class)->execute();

        self::assertTrue($prune->wasCalled);
        $this->assertDatabaseHas('backups', [
            'id' => $backup->id,
            'file_name' => 'prune-fail.zip',
        ]);
    }
}
