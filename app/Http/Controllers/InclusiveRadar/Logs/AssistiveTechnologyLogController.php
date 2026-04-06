<?php

namespace App\Http\Controllers\InclusiveRadar\Logs;

use App\Http\Controllers\Controller;
use App\Models\InclusiveRadar\AssistiveTechnology;
use Illuminate\Contracts\View\View;

class AssistiveTechnologyLogController extends Controller
{
    public function index(AssistiveTechnology $assistiveTechnology): View
    {
        $logs = $this->fetchLogs($assistiveTechnology, paginate: true);

        return view(
            'pages.inclusive-radar.assistive-technologies.logs.logs',
            compact('assistiveTechnology', 'logs')
        );
    }

    private function fetchLogs(AssistiveTechnology $assistiveTechnology, bool $paginate): mixed
    {
        $query = $assistiveTechnology->logs()
            ->with('user')
            ->latest();

        return $paginate ? $query->paginate(20) : $query->get();
    }
}
