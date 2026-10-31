<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Application\Actions\Impersonation;

use App\Domains\Auth\Application\Actions\Impersonation\StartImpersonationAction;
use App\Domains\Auth\Domain\Enums\ImpersonationMode;
use App\Domains\Auth\Domain\Exceptions\InvalidImpersonation;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

final class StartImpersonationActionTest extends TestCase
{
    public function test_it_starts_impersonation_for_super_admin_with_chosen_mode(): void
    {
        $impersonator = $this->user(id: 1, isSuperAdmin: true);
        $target = $this->user(id: 2, isAdmin: true);

        Auth::shouldReceive('login')->once()->with($target);

        $result = app(StartImpersonationAction::class)->execute($impersonator, $target, ImpersonationMode::FULL);

        self::assertTrue($result->is($target));
        self::assertSame(1, session('impersonator_id'));
        self::assertSame('full', session('impersonation_mode'));
    }

    public function test_it_rejects_common_admin_impersonator(): void
    {
        $this->expectException(InvalidImpersonation::class);
        $this->expectExceptionMessage('Apenas o administrador superior do Gnai pode entrar como outro usuário.');

        app(StartImpersonationAction::class)->execute(
            $this->user(id: 1, isAdmin: true),
            $this->user(id: 2),
            ImpersonationMode::READ_ONLY,
        );
    }

    public function test_it_rejects_impersonating_another_super_admin(): void
    {
        $this->expectException(InvalidImpersonation::class);
        $this->expectExceptionMessage('Não é possível entrar como outro administrador superior.');

        app(StartImpersonationAction::class)->execute(
            $this->user(id: 1, isSuperAdmin: true),
            $this->user(id: 2, isSuperAdmin: true),
            ImpersonationMode::READ_ONLY,
        );
    }

    public function test_it_rejects_impersonating_self(): void
    {
        $user = $this->user(id: 1, isSuperAdmin: true);

        $this->expectException(InvalidImpersonation::class);
        $this->expectExceptionMessage('Não é possível entrar como você mesmo.');

        app(StartImpersonationAction::class)->execute($user, $user, ImpersonationMode::READ_ONLY);
    }

    public function test_it_rejects_target_without_system_access(): void
    {
        $this->expectException(InvalidImpersonation::class);
        $this->expectExceptionMessage('Este usuário não tem acesso ao sistema.');

        app(StartImpersonationAction::class)->execute(
            $this->user(id: 1, isSuperAdmin: true),
            $this->user(id: 2, professionalId: null),
            ImpersonationMode::READ_ONLY,
        );
    }

    private function user(
        int $id,
        bool $isAdmin = false,
        bool $isSuperAdmin = false,
        ?int $professionalId = 10,
    ): User {
        return (new User)->forceFill([
            'id' => $id,
            'name' => "User {$id}",
            'email' => "user{$id}@example.test",
            'is_admin' => $isAdmin,
            'is_super_admin' => $isSuperAdmin,
            'professional_id' => $professionalId,
        ]);
    }
}
