<?php

declare(strict_types=1);

namespace App\Console\Commands\Auth;

use App\Domains\Auth\Application\Actions\Permissions\SyncPermissionsAction;
use Illuminate\Console\Command;

final class SyncPermissions extends Command
{
    protected $signature = 'permissions:sync {--prune : Remove permissões que não aparecem mais no código}';

    protected $description = 'Sincroniza permissões usadas no código com a tabela permissions';

    public function handle(SyncPermissionsAction $syncPermissions): int
    {
        $result = $syncPermissions->execute((bool) $this->option('prune'));

        $this->components->info(sprintf(
            'Permissões sincronizadas. Criadas: %d, atualizadas: %d, removidas: %d, total no código: %d.',
            $result['created'],
            $result['updated'],
            $result['deleted'],
            $result['total'],
        ));

        return Command::SUCCESS;
    }
}
