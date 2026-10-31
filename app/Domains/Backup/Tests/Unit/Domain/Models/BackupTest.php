<?php

declare(strict_types=1);

namespace App\Domains\Backup\Tests\Unit\Domain\Models;

use App\Domains\Backup\Domain\DTOs\CreateBackupDTO;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Exceptions\InvalidBackup;
use App\Domains\Backup\Domain\Models\Backup;
use Tests\TestCase;

final class BackupTest extends TestCase
{
    public function test_it_registers_a_backup(): void
    {
        $backup = Backup::register(new CreateBackupDTO(
            fileName: ' backup.zip ',
            filePath: ' GNAIbackups/backup.zip ',
            size: ' 10 MB ',
            status: BackupStatus::SUCCESS,
            userId: 10,
        ));

        self::assertSame('backup.zip', $backup->file_name);
        self::assertSame('GNAIbackups/backup.zip', $backup->file_path);
        self::assertSame('10 MB', $backup->size);
        self::assertSame(BackupStatus::SUCCESS, $backup->status);
        self::assertSame(10, $backup->user_id);
    }

    public function test_it_rejects_restore_when_backup_is_not_persisted(): void
    {
        $backup = Backup::register(new CreateBackupDTO(
            fileName: 'backup.zip',
            filePath: 'GNAIbackups/backup.zip',
            size: '10 MB',
            status: BackupStatus::SUCCESS,
        ));

        $this->expectException(InvalidBackup::class);
        $this->expectExceptionMessage('O backup precisa estar persistido antes da restauração.');

        $backup->ensureCanBeRestored();
    }

    public function test_it_rejects_restore_when_status_does_not_allow_restore(): void
    {
        $backup = Backup::register(new CreateBackupDTO(
            fileName: 'backup.zip',
            filePath: 'GNAIbackups/backup.zip',
            size: '10 MB',
            status: BackupStatus::FAILED,
        ));
        $backup->exists = true;

        $this->expectException(InvalidBackup::class);
        $this->expectExceptionMessage('Apenas backups concluídos com sucesso podem ser restaurados.');

        $backup->ensureCanBeRestored();
    }

    public function test_it_rejects_delete_when_backup_is_not_persisted(): void
    {
        $backup = Backup::register(new CreateBackupDTO(
            fileName: 'backup.zip',
            filePath: 'GNAIbackups/backup.zip',
            size: '10 MB',
            status: BackupStatus::SUCCESS,
        ));

        $this->expectException(InvalidBackup::class);
        $this->expectExceptionMessage('O backup precisa estar persistido antes da exclusão.');

        $backup->ensureCanBeDeleted();
    }

    public function test_it_rejects_delete_when_file_path_is_missing(): void
    {
        $backup = Backup::register(new CreateBackupDTO(
            fileName: 'backup.zip',
            filePath: '',
            size: '10 MB',
            status: BackupStatus::SUCCESS,
        ));
        $backup->exists = true;

        $this->expectException(InvalidBackup::class);
        $this->expectExceptionMessage('O backup selecionado não possui referência válida para o arquivo físico.');

        $backup->ensureCanBeDeleted();
    }
}
