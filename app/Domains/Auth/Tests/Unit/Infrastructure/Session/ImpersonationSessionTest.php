<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Infrastructure\Session;

use App\Domains\Auth\Domain\Enums\ImpersonationMode;
use App\Domains\Auth\Infrastructure\Session\ImpersonationSession;
use Tests\TestCase;

final class ImpersonationSessionTest extends TestCase
{
    public function test_it_starts_an_impersonation_with_the_chosen_mode(): void
    {
        $session = app(ImpersonationSession::class);

        $session->start(1, ImpersonationMode::FULL);

        self::assertTrue($session->active());
        self::assertSame(1, $session->impersonatorId());
        self::assertSame(ImpersonationMode::FULL, $session->mode());
        self::assertFalse($session->readOnly());
    }

    public function test_it_treats_a_session_without_valid_mode_as_read_only(): void
    {
        session(['impersonator_id' => 1, 'impersonation_mode' => 'unknown']);

        $session = app(ImpersonationSession::class);

        self::assertSame(ImpersonationMode::READ_ONLY, $session->mode());
        self::assertTrue($session->readOnly());
    }

    public function test_it_is_not_read_only_without_an_active_impersonation(): void
    {
        session()->forget(['impersonator_id', 'impersonation_mode']);

        $session = app(ImpersonationSession::class);

        self::assertFalse($session->active());
        self::assertNull($session->mode());
        self::assertFalse($session->readOnly());
    }

    public function test_it_clears_the_impersonator_and_the_mode(): void
    {
        $session = app(ImpersonationSession::class);
        $session->start(1, ImpersonationMode::READ_ONLY);

        $session->clear();

        self::assertFalse(session()->has('impersonator_id'));
        self::assertFalse(session()->has('impersonation_mode'));
    }
}
