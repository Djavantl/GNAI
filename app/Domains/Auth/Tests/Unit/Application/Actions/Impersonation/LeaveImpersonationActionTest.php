<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Application\Actions\Impersonation;

use App\Domains\Auth\Application\Actions\Impersonation\LeaveImpersonationAction;
use App\Domains\Auth\Application\Queries\Users\FindUserByIdQuery;
use App\Domains\Auth\Domain\Exceptions\InvalidImpersonation;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

final class LeaveImpersonationActionTest extends TestCase
{
    public function test_it_leaves_impersonation_and_logs_original_admin_back_in(): void
    {
        session(['impersonator_id' => 1]);
        $admin = $this->user(id: 1, isAdmin: true);

        $this->app->instance(FindUserByIdQuery::class, new class($admin) extends FindUserByIdQuery {
            public function __construct(private readonly User $user) {}

            public function execute(int $id): ?User
            {
                return $id === 1 ? $this->user : null;
            }
        });

        Auth::shouldReceive('login')->once()->with($admin);

        $result = app(LeaveImpersonationAction::class)->execute();

        self::assertTrue($result->is($admin));
        self::assertFalse(session()->has('impersonator_id'));
    }

    public function test_it_rejects_when_impersonation_is_not_active(): void
    {
        session()->forget('impersonator_id');

        $this->app->instance(FindUserByIdQuery::class, new class extends FindUserByIdQuery {
            public function execute(int $id): ?User
            {
                self::fail('User query should not be executed when impersonation is inactive.');
            }
        });

        $this->expectException(InvalidImpersonation::class);
        $this->expectExceptionMessage('Você não está em uma impersonação.');

        app(LeaveImpersonationAction::class)->execute();
    }

    public function test_it_clears_session_and_rejects_invalid_original_admin(): void
    {
        session(['impersonator_id' => 1]);

        $this->app->instance(FindUserByIdQuery::class, new class extends FindUserByIdQuery {
            public function execute(int $id): ?User
            {
                return null;
            }
        });

        Auth::shouldReceive('login')->never();

        $this->expectException(InvalidImpersonation::class);
        $this->expectExceptionMessage('Administrador original inválido ou sem permissão.');

        try {
            app(LeaveImpersonationAction::class)->execute();
        } finally {
            self::assertFalse(session()->has('impersonator_id'));
        }
    }

    public function test_it_clears_session_and_rejects_non_admin_original_user(): void
    {
        session(['impersonator_id' => 1]);
        $user = $this->user(id: 1);

        $this->app->instance(FindUserByIdQuery::class, new class($user) extends FindUserByIdQuery {
            public function __construct(private readonly User $user) {}

            public function execute(int $id): ?User
            {
                return $id === 1 ? $this->user : null;
            }
        });

        Auth::shouldReceive('login')->never();

        $this->expectException(InvalidImpersonation::class);
        $this->expectExceptionMessage('Administrador original inválido ou sem permissão.');

        try {
            app(LeaveImpersonationAction::class)->execute();
        } finally {
            self::assertFalse(session()->has('impersonator_id'));
        }
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
