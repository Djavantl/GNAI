<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Feature\Impersonation;

use App\Domains\Auth\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ReadOnlyImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_blocks_writes_in_read_only_mode(): void
    {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $target = User::factory()->create(['name' => 'Nome Original', 'is_admin' => true]);

        $response = $this->actingAs($target)
            ->withSession(['impersonator_id' => $superAdmin->id, 'impersonation_mode' => 'read_only'])
            ->from(route('profile.edit'))
            ->put(route('profile.account.update'), [
                'name' => 'Nome Alterado',
                'email' => $target->email,
            ]);

        $response
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('warning', 'Você está no modo somente leitura. Volte para a sua conta e entre novamente com acesso completo para fazer alterações.');
        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => 'Nome Original',
        ]);
    }

    public function test_it_rejects_json_writes_in_read_only_mode(): void
    {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $target = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($target)
            ->withSession(['impersonator_id' => $superAdmin->id, 'impersonation_mode' => 'read_only'])
            ->putJson(route('profile.account.update'), [
                'name' => 'Nome Alterado',
                'email' => $target->email,
            ]);

        $response
            ->assertForbidden()
            ->assertJsonPath('message', 'Você está no modo somente leitura. Volte para a sua conta e entre novamente com acesso completo para fazer alterações.');
    }

    public function test_it_allows_reading_in_read_only_mode(): void
    {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $target = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($target)
            ->withSession(['impersonator_id' => $superAdmin->id, 'impersonation_mode' => 'read_only'])
            ->get(route('profile.edit'));

        $response->assertOk();
    }

    public function test_it_allows_leaving_the_impersonation_in_read_only_mode(): void
    {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $target = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($target)
            ->withSession(['impersonator_id' => $superAdmin->id, 'impersonation_mode' => 'read_only'])
            ->post(route('admin.impersonate.leave'));

        $response
            ->assertRedirect(route('dashboard'))
            ->assertSessionMissing('impersonator_id')
            ->assertSessionMissing('impersonation_mode');
        $this->assertAuthenticatedAs($superAdmin);
    }

    public function test_it_allows_writes_in_full_mode(): void
    {
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $target = User::factory()->create(['name' => 'Nome Original', 'is_admin' => true]);

        $response = $this->actingAs($target)
            ->withSession(['impersonator_id' => $superAdmin->id, 'impersonation_mode' => 'full'])
            ->from(route('profile.edit'))
            ->put(route('profile.account.update'), [
                'name' => 'Nome Alterado',
                'email' => $target->email,
            ]);

        $response
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success', 'Seu perfil foi atualizado com sucesso!');
        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => 'Nome Alterado',
        ]);
    }
}
