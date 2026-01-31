<?php

namespace App\Services\Report;

use App\Services\Report\Modules\AssistiveTechnologyReportModule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class ReportService
{
    protected array $availableModules;

    public function __construct()
    {
        $this->availableModules = [
            'assistive_technologies' => new AssistiveTechnologyReportModule(),
        ];
    }

    protected function buildSections(array $modules, array $filters): Collection
    {
        $sections = collect();

        foreach ($modules as $moduleKey) {
            if (!isset($this->availableModules[$moduleKey])) {
                continue;
            }

            $module = $this->availableModules[$moduleKey];

            $sections->push([
                'key'   => $moduleKey,
                'title' => $module->getTitle(),
                'data'  => $module->getData($filters),
            ]);
        }

        return $sections;
    }

    public function generate(
        array $modules,
        array $filters,
        array $layout,
        string $format
    ) {
        $sections = $this->buildSections($modules, $filters);

        if ($format === 'pdf') {
            return Pdf::loadView('reports.template', [
                'sections' => $sections,
                'layout'   => $layout,
            ])->download('relatorio.pdf');
        }

        return view('reports.index', [
            'sections' => $sections,
            'layout'   => $layout,
        ]);
    }
}
