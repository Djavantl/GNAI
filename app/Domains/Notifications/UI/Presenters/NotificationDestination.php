<?php

declare(strict_types=1);

namespace App\Domains\Notifications\UI\Presenters;

final class NotificationDestination
{
    public static function toLocalUrl(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $parts = parse_url(trim($url));

        if ($parts === false) {
            return null;
        }

        $path = $parts['path'] ?? '/';
        $path = '/'.ltrim($path, '/');

        if (isset($parts['query'])) {
            $path .= '?'.$parts['query'];
        }

        if (isset($parts['fragment'])) {
            $path .= '#'.$parts['fragment'];
        }

        return $path;
    }
}
