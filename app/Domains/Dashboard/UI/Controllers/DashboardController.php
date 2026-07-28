<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\UI\Controllers;

use App\Domains\Dashboard\Application\Queries\DashboardMetricsQuery;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function index(DashboardMetricsQuery $query): View
    {
        return view('pages.dashboard', $query->execute());
    }
}
