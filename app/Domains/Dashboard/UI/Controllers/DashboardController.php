<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\UI\Controllers;

use App\Domains\Dashboard\Application\Queries\DashboardMetricsQuery;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class DashboardController
{
    public function index(DashboardMetricsQuery $query): InertiaResponse
    {
        return Inertia::render('Dashboard', $query->execute());
    }
}
