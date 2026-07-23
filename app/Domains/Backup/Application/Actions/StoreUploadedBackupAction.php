<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Actions;

use App\Domains\Backup\Application\Data\UploadBackupData;
use App\Domains\Backup\Domain\DTOs\CreateBackupDTO;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Backup\Infrastructure\Storage\BackupArchiveStorageContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

final readonly class StoreUploadedBackupAction
{
    public function __construct(
        private BackupArchiveStorageContract $archives,
        private PruneBackupsActionContract $pruneBackups,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(UploadBackupData $data, ?int $userId = null): Backup
    {
        $resolvedUserId = $userId ?? Auth::id();

        if ($resolvedUserId === null) {
            throw new RuntimeException('Não foi possível identificar o usuário responsável pelo upload do backup.');
        }

        try {
            $archive = $this->archives->storeUploadedArchive($data->backupFile);
        } catch (Throwable $exception) {
            Log::error('Falha ao armazenar arquivo de backup enviado.', [
                'user_id' => $resolvedUserId,
                'original_file_name' => $data->backupFile->getClientOriginalName(),
                'exception' => $exception,
            ]);

            throw $exception;
        }

        try {
            $backupDTO = new CreateBackupDTO(
                fileName: $archive->fileName,
                filePath: $archive->filePath,
                size: $archive->size,
                status: BackupStatus::SUCCESS,
                userId: $resolvedUserId,
            );

            $backup = Backup::register($backupDTO);
            $backup->save();
        } catch (Throwable $exception) {
            if ($this->archives->archiveExists($archive->filePath)) {
                $deleted = $this->archives->deleteArchive($archive->filePath);

                if (! $deleted) {
                    Log::warning('Arquivo de backup enviado ficou órfão após falha ao registrar no banco.', [
                        'user_id' => $resolvedUserId,
                        'file_name' => $archive->fileName,
                        'file_path' => $archive->filePath,
                        'persistence_exception' => $exception,
                    ]);
                }
            }

            Log::error('Falha ao registrar backup enviado.', [
                'user_id' => $resolvedUserId,
                'file_name' => $archive->fileName,
                'file_path' => $archive->filePath,
                'exception' => $exception,
            ]);

            throw $exception;
        }

        try {
            $this->pruneBackups->execute();
        } catch (Throwable $exception) {
            Log::error('Falha ao podar backups antigos após upload bem-sucedido.', [
                'backup_id' => $backup->id,
                'file_name' => $backup->file_name,
                'user_id' => $resolvedUserId,
                'exception' => $exception,
            ]);
        }

        return $backup;
    }
}
