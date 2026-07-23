<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Actions;

use App\Domains\Backup\Application\Contracts\BackupArchiveStorageContract;
use App\Domains\Backup\Application\Contracts\PruneBackupsActionContract;
use App\Domains\Backup\Domain\Models\Backup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class PruneBackupsAction implements PruneBackupsActionContract
{
    public const int MAX_BACKUPS = 30;

    public function __construct(
        private BackupArchiveStorageContract $archives,
    ) {}

    public function execute(): int
    {
        $keptBackups = Backup::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(self::MAX_BACKUPS)
            ->get(['id', 'file_path']);

        $keptIds = $keptBackups
            ->pluck('id')
            ->all();

        if ($keptIds === []) {
            return 0;
        }

        $keptFilePaths = $keptBackups
            ->pluck('file_path')
            ->all();

        /** @var Collection<int, Backup> $exceededBackups */
        $exceededBackups = Backup::query()
            ->whereNotIn('id', $keptIds)
            ->oldest()
            ->get(['id', 'file_name', 'file_path']);

        $keptFilePaths = array_flip($keptFilePaths);
        $deleted = 0;

        foreach ($exceededBackups as $backup) {
            $backupId = $backup->id;
            $fileName = $backup->file_name;
            $filePath = $backup->file_path;
            $shouldDeleteFile = ! isset($keptFilePaths[$filePath]);

            if (! $shouldDeleteFile) {
                Log::warning('Registro excedente de backup compartilha arquivo físico com um backup mantido.', [
                    'backup_id' => $backupId,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                ]);
            }

            try {
                DB::transaction(fn (): ?bool => $backup->delete());
            } catch (Throwable $exception) {
                Log::error('Falha ao remover registro de backup excedente.', [
                    'backup_id' => $backupId,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'exception' => $exception,
                ]);

                continue;
            }

            if ($shouldDeleteFile) {
                $fileExists = $this->archives->archiveExists($filePath);
                $fileDeleted = $fileExists && $this->archives->deleteArchive($filePath);

                if ($fileExists && ! $fileDeleted) {
                    Log::warning('Registro excedente de backup removido, mas o arquivo físico não pôde ser apagado.', [
                        'backup_id' => $backupId,
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                    ]);
                }
            }

            $deleted++;
        }

        return $deleted;
    }
}
