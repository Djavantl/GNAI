<?php

declare(strict_types=1);

namespace App\Domains\Website\Infrastructure\Providers;

use App\Domains\Audit\Infrastructure\AuditableEntities;
use App\Domains\Website\Domain\Models\WebsiteSetting;
use Illuminate\Support\ServiceProvider;

final class WebsiteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->make(AuditableEntities::class)->register(
            WebsiteSetting::class,
        );
    }
}
