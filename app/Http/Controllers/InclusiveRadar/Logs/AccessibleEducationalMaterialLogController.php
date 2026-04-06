<?php

namespace App\Http\Controllers\InclusiveRadar\Logs;

use App\Http\Controllers\Controller;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use Illuminate\Contracts\View\View;

class AccessibleEducationalMaterialLogController extends Controller
{
    public function index(AccessibleEducationalMaterial $material): View
    {
        $logs = $this->fetchLogs($material, paginate: true);

        return view(
            'pages.inclusive-radar.accessible-educational-materials.logs.logs',
            compact('material', 'logs')
        );
    }

    private function fetchLogs(AccessibleEducationalMaterial $material, bool $paginate): mixed
    {
        $query = $material->logs()
            ->with('user')
            ->latest();

        return $paginate ? $query->paginate(20) : $query->get();
    }
}
