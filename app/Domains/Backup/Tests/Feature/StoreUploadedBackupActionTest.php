<?php

declare(strict_types=1);

namespace App\Domains\Backup\Tests\Feature;

use App\Domains\Backup\Application\Actions\StoreUploadedBackupAction;
use App\Domains\Backup\Application\Contracts\BackupArchiveStorageContract;
use App\Domains\Backup\Application\Data\UploadBackupData;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Exceptions\BackupOperationFailed;
use App\Domains\Backup\Tests\Fakes\FakeBackupArchiveStorage;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

final class StoreUploadedBackupActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_uploaded_backup_with_responsible_user(): void
    {
        $user = User::factory()->create();
        $storage = new FakeBackupArchiveStorage;

        $this->app->instance(BackupArchiveStorageContract::class, $storage);

        $backup = app(StoreUploadedBackupAction::class)->execute(
            new UploadBackupData(
                backupFile: UploadedFile::fake()->create('manual.zip', 1, 'application/zip'),
            ),
            $user->id,
        );

        $this->assertDatabaseHas('backups', [
            'id' => $backup->id,
            'file_name' => 'uploaded-backup-test.zip',
            'file_path' => 'GNAIbackups/uploaded-backup-test.zip',
            'status' => BackupStatus::SUCCESS->value,
            'user_id' => $user->id,
        ]);
    }

    public function test_it_requires_a_responsible_user_for_upload(): void
    {
        $this->app->instance(BackupArchiveStorageContract::class, new FakeBackupArchiveStorage);

        $this->expectException(BackupOperationFailed::class);
        $this->expectExceptionMessage('Não foi possível identificar o usuário responsável pelo upload do backup.');

        app(StoreUploadedBackupAction::class)->execute(
            new UploadBackupData(
                backupFile: UploadedFile::fake()->create('manual.zip', 1, 'application/zip'),
            ),
        );
    }
}
