<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Infrastructure\Session;

use App\Domains\Auth\Infrastructure\Session\ImpersonationState;
use Tests\TestCase;

final class ImpersonationStateTest extends TestCase
{
    public function test_it_detects_active_impersonation_session(): void
    {
        session(['impersonator_id' => 1]);

        self::assertTrue(app(ImpersonationState::class)->active());
    }

    public function test_it_detects_inactive_impersonation_session(): void
    {
        session()->forget('impersonator_id');

        self::assertFalse(app(ImpersonationState::class)->active());
    }
}
