<?php

declare(strict_types=1);

namespace App\Domains\Backup\Tests\Fakes;

use App\Domains\Backup\Application\Contracts\BackupArchiveStorageContract;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Backup\Infrastructure\Storage\BackupArchiveMetadata;
use Illuminate\Http\UploadedFile;
use Throwable;

final class FakeBackupArchiveStorage implements BackupArchiveStorageContract
{
    public ?BackupArchiveMetadata $generatedArchive = null;

    public ?Throwable $generateException = null;

    public ?BackupArchiveMetadata $uploadedArchive = null;

    /** @var list<BackupArchiveMetadata> */
    public array $storedArchives = [];

    /** @var array<string, bool> */
    public array $existingArchives = [];

    /** @var array<string, bool> */
    public array $deleteResults = [];

    /** @var list<string> */
    public array $deletedArchives = [];

    public bool $backupArchiveExists = true;

    public bool $restoreWasCalled = false;

    public function generateArchive(): BackupArchiveMetadata
    {
        if ($this->generateException !== null) {
            throw $this->generateException;
        }

        return $this->generatedArchive ?? new BackupArchiveMetadata(
            fileName: 'generated.zip',
            filePath: 'GNAIbackups/generated.zip',
            size: '1 MB',
        );
    }

    public function storeUploadedArchive(UploadedFile $file): BackupArchiveMetadata
    {
        return $this->uploadedArchive ?? new BackupArchiveMetadata(
            fileName: 'uploaded-backup-test.zip',
            filePath: 'GNAIbackups/uploaded-backup-test.zip',
            size: '1 MB',
        );
    }

    public function deleteArchive(string $filePath): bool
    {
        $this->deletedArchives[] = $filePath;

        return $this->deleteResults[$filePath] ?? true;
    }

    public function archiveExists(string $filePath): bool
    {
        return $this->existingArchives[$filePath] ?? false;
    }

    public function backupArchiveExists(Backup $backup): bool
    {
        return $this->backupArchiveExists;
    }

    public function listStoredArchives(): array
    {
        return $this->storedArchives;
    }

    public function restoreArchive(Backup $backup): void
    {
        $this->restoreWasCalled = true;
    }
}
