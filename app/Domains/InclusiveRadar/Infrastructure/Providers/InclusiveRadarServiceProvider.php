<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Providers;

use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Domains\InclusiveRadar\UI\Console\Commands\CheckOverdueLoans;
use App\Domains\InclusiveRadar\UI\Console\Commands\SendInstitutionalEventReminders;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

final class InclusiveRadarServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            CheckOverdueLoans::class,
            SendInstitutionalEventReminders::class,
        ]);

        View::composer('layouts.master', function ($view): void {
            $view->with('institution', $this->resolveInstitutionForLayout());
        });
    }

    private function resolveInstitutionForLayout(): ?Institution
    {
        if (! $this->canUseInstitutionsTable()) {
            return null;
        }

        return Institution::query()->first();
    }

    private function canUseInstitutionsTable(): bool
    {
        try {
            return Schema::hasTable('institutions');
        } catch (Throwable) {
            return false;
        }
    }
}
