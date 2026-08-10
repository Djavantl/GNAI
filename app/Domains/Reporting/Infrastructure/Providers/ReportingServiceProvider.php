<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Infrastructure\Providers;

use App\Domains\Reporting\Application\Services\ReportCatalog;
use Illuminate\Support\ServiceProvider;

final class ReportingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(ReportCatalog::class);
    }
}
