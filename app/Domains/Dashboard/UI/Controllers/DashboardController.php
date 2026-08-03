<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\UI\Controllers;

use App\Domains\Dashboard\Application\Queries\DashboardMetricsQuery;
use Illuminate\View\View;

final class DashboardController
{
    public function index(DashboardMetricsQuery $query): View
    {
        return view('pages.dashboard', $query->execute());
    }
}
