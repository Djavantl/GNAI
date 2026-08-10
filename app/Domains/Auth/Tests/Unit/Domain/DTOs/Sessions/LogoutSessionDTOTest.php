<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Domain\DTOs\Sessions;

use App\Domains\Auth\Domain\DTOs\Sessions\LogoutSessionDTO;
use Tests\TestCase;

final class LogoutSessionDTOTest extends TestCase
{
    public function test_it_clears_impersonation_by_default(): void
    {
        self::assertTrue((new LogoutSessionDTO)->clearImpersonation);
    }
}
