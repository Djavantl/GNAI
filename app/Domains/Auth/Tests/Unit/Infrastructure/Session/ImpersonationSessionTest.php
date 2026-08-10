<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Infrastructure\Session;

use App\Domains\Auth\Infrastructure\Session\ImpersonationSession;
use Tests\TestCase;

final class ImpersonationSessionTest extends TestCase
{
    public function test_it_detects_active_impersonation_session(): void
    {
        session(['impersonator_id' => 1]);

        self::assertTrue(app(ImpersonationSession::class)->active());
    }

    public function test_it_detects_inactive_impersonation_session(): void
    {
        session()->forget('impersonator_id');

        self::assertFalse(app(ImpersonationSession::class)->active());
    }
}
