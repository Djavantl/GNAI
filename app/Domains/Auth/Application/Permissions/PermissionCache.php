<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Permissions;

use Illuminate\Support\Facades\Cache;

final class PermissionCache
{
    private const VERSION_KEY = 'auth.permissions.version';

    private ?int $version = null;

    public function version(): int
    {
        if ($this->version !== null) {
            return $this->version;
        }

        $version = Cache::get(self::VERSION_KEY);

        if ($version === null) {
            Cache::forever(self::VERSION_KEY, 1);

            return $this->version = 1;
        }

        return $this->version = (int) $version;
    }

    public function invalidate(): void
    {
        if (Cache::get(self::VERSION_KEY) === null) {
            Cache::forever(self::VERSION_KEY, 1);
        }

        $this->version = Cache::increment(self::VERSION_KEY);
    }
}
