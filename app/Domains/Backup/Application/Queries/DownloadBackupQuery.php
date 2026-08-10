<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Queries;

use App\Domains\Backup\Application\Contracts\BackupArchiveStorageContract;
use App\Domains\Backup\Domain\Models\Backup;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class DownloadBackupQuery
{
    public function __construct(
        private BackupArchiveStorageContract $archives,
    ) {}

    public function execute(Backup $backup): ?StreamedResponse
    {
        if (! $this->archives->archiveExists($backup->file_path)) {
            return null;
        }

        return Storage::disk('local')->download($backup->file_path, $backup->file_name);
    }
}
