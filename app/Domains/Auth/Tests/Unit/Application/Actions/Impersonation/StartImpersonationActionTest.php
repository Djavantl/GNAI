<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Application\Actions\Impersonation;

use App\Domains\Auth\Application\Actions\Impersonation\StartImpersonationAction;
use App\Domains\Auth\Domain\Exceptions\InvalidImpersonation;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

final class StartImpersonationActionTest extends TestCase
{
    public function test_it_starts_impersonation_for_admin_user(): void
    {
        $impersonator = $this->user(id: 1, isAdmin: true);
        $target = $this->user(id: 2);

        Auth::shouldReceive('login')->once()->with($target);

        $result = app(StartImpersonationAction::class)->execute($impersonator, $target);

        self::assertTrue($result->is($target));
        self::assertSame(1, session('impersonator_id'));
    }

    public function test_it_rejects_non_admin_impersonator(): void
    {
        $this->expectException(InvalidImpersonation::class);
        $this->expectExceptionMessage('Apenas administradores podem impersonar usuários.');

        app(StartImpersonationAction::class)->execute(
            $this->user(id: 1),
            $this->user(id: 2),
        );
    }

    public function test_it_rejects_impersonating_another_admin(): void
    {
        $this->expectException(InvalidImpersonation::class);
        $this->expectExceptionMessage('Não é possível impersonar outro administrador.');

        app(StartImpersonationAction::class)->execute(
            $this->user(id: 1, isAdmin: true),
            $this->user(id: 2, isAdmin: true),
        );
    }

    public function test_it_rejects_impersonating_self(): void
    {
        $user = $this->user(id: 1, isAdmin: true);

        $this->expectException(InvalidImpersonation::class);
        $this->expectExceptionMessage('Não é possível impersonar você mesmo.');

        app(StartImpersonationAction::class)->execute($user, $user);
    }

    private function user(int $id, bool $isAdmin = false): User
    {
        return (new User)->forceFill([
            'id' => $id,
            'name' => "User {$id}",
            'email' => "user{$id}@example.test",
            'is_admin' => $isAdmin,
        ]);
    }
}
