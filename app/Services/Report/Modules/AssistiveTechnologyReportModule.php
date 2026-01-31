<?php

namespace App\Services\Report\Modules;

use Illuminate\Support\Collection;
use App\Models\InclusiveRadar\AssistiveTechnology;

class AssistiveTechnologyReportModule implements ReportModuleInterface
{
    public function getKey(): string
    {
        return 'assistive_technologies';
    }

    public function getTitle(): string
    {
        return 'Tecnologias Assistivas';
    }

    public function getData(array $filters): Collection
    {
        return AssistiveTechnology::with(['type', 'resourceStatus'])
            ->orderBy('name')
            ->get();
    }
}
