<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Actions;

use App\Domains\Backup\Domain\DTOs\CreateBackupDTO;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Backup\Infrastructure\Storage\BackupArchiveStorageContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class SyncBackupsAction
{
    public function __construct(
        private BackupArchiveStorageContract $archives,
        private PruneBackupsActionContract $pruneBackups,
    ) {}

    public function execute(?int $userId = null): array
    {
        $resolvedUserId = $userId ?? Auth::id();

        $created = 0;
        $removed = 0;
        $pruned = 0;

        try {
            $knownFilePaths = Backup::query()->pluck('file_path')->flip();

            foreach ($this->archives->listStoredArchives() as $archive) {
                if ($knownFilePaths->has($archive->filePath)) {
                    continue;
                }

                $backupDTO = new CreateBackupDTO(
                    fileName: $archive->fileName,
                    filePath: $archive->filePath,
                    size: $archive->size,
                    status: BackupStatus::SUCCESS,
                    userId: $resolvedUserId,
                );

                Backup::register($backupDTO)->save();
                $knownFilePaths->put($archive->filePath, true);
                $created++;
            }
        } catch (Throwable $exception) {
            Log::error('Falha ao sincronizar arquivos de backup do disco para o banco.', [
                'user_id' => $resolvedUserId,
                'created_before_failure' => $created,
                'exception' => $exception,
            ]);

            return ['success' => false, 'created' => $created, 'removed' => 0, 'pruned' => 0];
        }

        try {
            Backup::query()->chunkById(100, function ($backups) use (&$removed): void {
                foreach ($backups as $backup) {
                    $backupId = $backup->id;
                    $fileName = $backup->file_name;
                    $filePath = $backup->file_path;

                    if (! $this->archives->archiveExists($filePath)) {
                        Log::warning('Registro de backup removido: arquivo físico não encontrado.', [
                            'backup_id' => $backupId,
                            'file_name' => $fileName,
                            'file_path' => $filePath,
                        ]);

                        try {
                            DB::transaction(fn (): ?bool => $backup->delete());
                            $removed++;
                        } catch (Throwable $exception) {
                            Log::error('Falha ao remover registro órfão de backup.', [
                                'backup_id' => $backupId,
                                'file_name' => $fileName,
                                'file_path' => $filePath,
                                'exception' => $exception,
                            ]);
                        }
                    }
                }
            });
        } catch (Throwable $exception) {
            Log::error('Falha ao remover registros de backup órfãos.', [
                'removed_before_failure' => $removed,
                'exception' => $exception,
            ]);

            return ['success' => false, 'created' => $created, 'removed' => $removed, 'pruned' => 0];
        }

        try {
            $pruned = $this->pruneBackups->execute();
        } catch (Throwable $exception) {
            Log::error('Falha ao podar backups antigos após sincronização.', [
                'user_id' => $resolvedUserId,
                'exception' => $exception,
            ]);
        }

        return ['success' => true, 'created' => $created, 'removed' => $removed, 'pruned' => $pruned];
    }
}
