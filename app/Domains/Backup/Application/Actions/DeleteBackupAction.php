<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Actions;

use App\Domains\Backup\Domain\Exceptions\InvalidBackup;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Backup\Infrastructure\Storage\BackupArchiveStorageContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class DeleteBackupAction
{
    public function __construct(
        private BackupArchiveStorageContract $archives,
    ) {}

    /**
     * @throws InvalidBackup
     * @throws Throwable
     */
    public function execute(Backup $backup): void
    {
        $backup->ensureCanBeDeleted();

        Log::info('Iniciando exclusão de backup.', [
            'backup_id' => $backup->id,
            'file_name' => $backup->file_name,
            'file_path' => $backup->file_path,
        ]);

        try {
            $backupId = $backup->id;
            $fileName = $backup->file_name;
            $filePath = $backup->file_path;
            $fileExists = $this->archives->archiveExists($filePath);

            DB::transaction(fn (): ?bool => $backup->delete());

            $fileDeleted = false;

            if ($fileExists) {
                $fileDeleted = $this->archives->deleteArchive($filePath);

                if (! $fileDeleted) {
                    Log::warning('Registro de backup removido, mas o arquivo físico não pôde ser apagado.', [
                        'backup_id' => $backupId,
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                    ]);
                }
            }

            Log::info('Backup excluído.', [
                'backup_id' => $backupId,
                'file_name' => $fileName,
                'file_deleted' => $fileDeleted,
            ]);
        } catch (Throwable $exception) {
            Log::error('Falha ao excluir backup.', [
                'backup_id' => $backup->id,
                'file_name' => $backup->file_name,
                'exception' => $exception,
            ]);

            throw $exception;
        }
    }
}
