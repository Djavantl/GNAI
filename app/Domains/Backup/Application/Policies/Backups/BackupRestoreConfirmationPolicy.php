<?php

declare(strict_types=1);

namespace App\Domains\Backup\Application\Policies\Backups;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Backup\Domain\Exceptions\InvalidBackupRestoreConfirmation;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

final readonly class BackupRestoreConfirmationPolicy
{
    /**
     * @throws InvalidBackupRestoreConfirmation
     */
    public function ensurePasswordMatches(?Authenticatable $user, string $password): void
    {
        if (! $user instanceof User || ! Hash::check($password, $user->password)) {
            throw new InvalidBackupRestoreConfirmation(
                'Senha incorreta. A restauração não foi executada.'
            );
        }
    }
}
