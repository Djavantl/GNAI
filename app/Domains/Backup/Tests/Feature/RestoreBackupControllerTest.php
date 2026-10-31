<?php

declare(strict_types=1);

namespace App\Domains\Backup\Tests\Feature;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Backup\Application\Contracts\BackupArchiveStorageContract;
use App\Domains\Backup\Domain\DTOs\CreateBackupDTO;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Backup\Tests\Fakes\FakeBackupArchiveStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class RestoreBackupControllerTest extends TestCase
{
    use RefreshDatabase;

    private FakeBackupArchiveStorage $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = new FakeBackupArchiveStorage;
        $this->storage->backupArchiveExists = true;

        $this->app->instance(BackupArchiveStorageContract::class, $this->storage);
    }

    public function test_it_requires_the_authenticated_user_password_to_restore_backup(): void
    {
        $user = $this->admin();
        $backup = $this->backup();

        $response = $this->actingAs($user)
            ->from(route('backup.backups.index'))
            ->post(route('backup.backups.restore', $backup), []);

        $response->assertRedirect(route('backup.backups.index'));
        $response->assertSessionHasErrors([
            'password' => 'Informe sua senha para confirmar a restauração.',
        ]);
        self::assertFalse($this->storage->restoreWasCalled);
    }

    public function test_it_rejects_restore_when_password_is_invalid(): void
    {
        $user = $this->admin();
        $backup = $this->backup();

        $response = $this->actingAs($user)
            ->from(route('backup.backups.index'))
            ->post(route('backup.backups.restore', $backup), [
                'password' => 'senha-errada',
            ]);

        $response->assertRedirect(route('backup.backups.index'));
        $response->assertSessionHas('error', 'Senha incorreta. A restauração não foi executada.');
        self::assertFalse($this->storage->restoreWasCalled);
    }

    public function test_it_restores_backup_when_password_is_valid(): void
    {
        $user = $this->admin();
        $backup = $this->backup();

        $response = $this->actingAs($user)
            ->post(route('backup.backups.restore', $backup), [
                'password' => 'senha-correta',
            ]);

        $response->assertRedirect(route('backup.backups.index'));
        $response->assertSessionHas('success', 'Sistema restaurado com sucesso para a versão selecionada!');
        self::assertTrue($this->storage->restoreWasCalled);
    }

    public function test_it_forbids_restore_without_permission(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('senha-correta'),
        ]);
        $backup = $this->backup();

        $response = $this->actingAs($user)
            ->post(route('backup.backups.restore', $backup), [
                'password' => 'senha-correta',
            ]);

        $response->assertForbidden();
        self::assertFalse($this->storage->restoreWasCalled);
    }

    private function admin(): User
    {
        return User::factory()->create([
            'password' => Hash::make('senha-correta'),
            'is_admin' => true,
        ]);
    }

    private function backup(): Backup
    {
        $backup = Backup::register(new CreateBackupDTO(
            fileName: 'backup.zip',
            filePath: 'GNAIbackups/backup.zip',
            size: '1 MB',
            status: BackupStatus::SUCCESS,
        ));
        $backup->save();

        return $backup;
    }
}
