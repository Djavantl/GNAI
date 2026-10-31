<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Providers;

use App\Domains\Audit\Infrastructure\AuditableEntities;
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\Models\InspectionEvidence;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\InclusiveRadar\Domain\Models\Location;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
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

        $this->app->make(AuditableEntities::class)->register(
            AccessibilityFeature::class,
            AccessibleEducationalMaterial::class,
            AssistiveTechnology::class,
            Barrier::class,
            BarrierCategory::class,
            Inspection::class,
            InspectionEvidence::class,
            Institution::class,
            InstitutionalEvent::class,
            Loan::class,
            Location::class,
            Waitlist::class,
        );
    }
}
