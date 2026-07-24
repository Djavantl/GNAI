<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Application\Actions\Sessions;

use App\Domains\Auth\Application\Actions\Sessions\LogoutUserAction;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

final class LogoutUserActionTest extends TestCase
{
    public function test_it_logs_out_and_clears_impersonation_state(): void
    {
        Auth::shouldReceive('logout')->once();
        session(['impersonator_id' => 1]);

        app(LogoutUserAction::class)->execute();

        self::assertFalse(session()->has('impersonator_id'));
    }
}
