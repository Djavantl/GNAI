<?php

declare(strict_types=1);

namespace App\Domains\Backup\UI\Console\Commands;

use App\Domains\Backup\Application\Actions\GenerateBackupAction;
use Illuminate\Console\Command;

class BackupAutomatic extends Command
{
    protected $signature = 'backup:automatic';

    protected $description = 'Executa backup automático do sistema';

    public function handle(GenerateBackupAction $generateBackup): int
    {
        $this->info('Iniciando backup automático...');

        $generateBackup->execute();

        $this->info('Backup concluído com sucesso.');

        return Command::SUCCESS;
    }
}
