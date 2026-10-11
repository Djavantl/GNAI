<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Providers;

use App\Domains\InclusiveRadar\UI\Console\Commands\CheckOverdueLoans;
use App\Domains\InclusiveRadar\UI\Console\Commands\SendInstitutionalEventReminders;
use Illuminate\Support\ServiceProvider;

final class InclusiveRadarServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            CheckOverdueLoans::class,
            SendInstitutionalEventReminders::class,
        ]);
    }
}
