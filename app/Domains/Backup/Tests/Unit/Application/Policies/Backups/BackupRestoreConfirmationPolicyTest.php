<?php

declare(strict_types=1);

namespace App\Domains\Backup\Tests\Unit\Application\Policies\Backups;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Backup\Application\Policies\Backups\BackupRestoreConfirmationPolicy;
use App\Domains\Backup\Domain\Exceptions\InvalidBackupRestoreConfirmation;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class BackupRestoreConfirmationPolicyTest extends TestCase
{
    public function test_it_accepts_the_authenticated_user_password(): void
    {
        $user = User::factory()->make([
            'password' => Hash::make('senha-correta'),
        ]);

        app(BackupRestoreConfirmationPolicy::class)->ensurePasswordMatches(
            user: $user,
            password: 'senha-correta',
        );

        $this->expectNotToPerformAssertions();
    }

    public function test_it_rejects_an_invalid_password(): void
    {
        $user = User::factory()->make([
            'password' => Hash::make('senha-correta'),
        ]);

        $this->expectException(InvalidBackupRestoreConfirmation::class);
        $this->expectExceptionMessage('Senha incorreta. A restauração não foi executada.');

        app(BackupRestoreConfirmationPolicy::class)->ensurePasswordMatches(
            user: $user,
            password: 'senha-errada',
        );
    }

    public function test_it_rejects_a_missing_user(): void
    {
        $this->expectException(InvalidBackupRestoreConfirmation::class);
        $this->expectExceptionMessage('Senha incorreta. A restauração não foi executada.');

        app(BackupRestoreConfirmationPolicy::class)->ensurePasswordMatches(
            user: null,
            password: 'senha-correta',
        );
    }
}
