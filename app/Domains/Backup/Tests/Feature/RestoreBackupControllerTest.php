<?php

declare(strict_types=1);

namespace App\Domains\Backup\Tests\Feature;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Backup\Application\Contracts\BackupArchiveStorageContract;
use App\Domains\Backup\Domain\DTOs\CreateBackupDTO;
use App\Domains\Backup\Domain\Enums\BackupStatus;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Backup\Tests\Fakes\FakeBackupArchiveStorage;
use App\Domains\Backup\UI\Controllers\BackupController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class RestoreBackupControllerTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/_tests/backups';

    private FakeBackupArchiveStorage $storage;

    protected function setUp(): void
    {
        parent::setUp();

        Route::post(
            self::ENDPOINT.'/{backup}/restore',
            [BackupController::class, 'restore'],
        )->middleware('web');

        $this->storage = new FakeBackupArchiveStorage;
        $this->storage->backupArchiveExists = true;

        $this->app->instance(BackupArchiveStorageContract::class, $this->storage);
    }

    public function test_it_requires_the_authenticated_user_password_to_restore_backup(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('senha-correta'),
        ]);
        $backup = $this->backup();

        $response = $this->actingAs($user)
            ->from('/backup/backups')
            ->post(self::ENDPOINT.'/'.$backup->id.'/restore', []);

        $response->assertRedirect('/backup/backups');
        $response->assertSessionHasErrors([
            'password' => 'Informe sua senha para confirmar a restauração.',
        ]);
        self::assertFalse($this->storage->restoreWasCalled);
    }

    public function test_it_rejects_restore_when_password_is_invalid(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('senha-correta'),
        ]);
        $backup = $this->backup();

        $response = $this->actingAs($user)
            ->from('/backup/backups')
            ->post(self::ENDPOINT.'/'.$backup->id.'/restore', [
                'password' => 'senha-errada',
            ]);

        $response->assertRedirect('/backup/backups');
        $response->assertSessionHas('error', 'Senha incorreta. A restauração não foi executada.');
        self::assertFalse($this->storage->restoreWasCalled);
    }

    public function test_it_restores_backup_when_password_is_valid(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('senha-correta'),
        ]);
        $backup = $this->backup();

        $response = $this->actingAs($user)
            ->post(self::ENDPOINT.'/'.$backup->id.'/restore', [
                'password' => 'senha-correta',
            ]);

        $response->assertRedirect(route('backup.backups.index'));
        $response->assertSessionHas('success', 'Sistema restaurado com sucesso para a versão selecionada!');
        self::assertTrue($this->storage->restoreWasCalled);
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
