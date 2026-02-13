<?php

namespace App\Http\Controllers\Report;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Models\InclusiveRadar\ResourceType;
use App\Models\InclusiveRadar\AccessibilityFeature;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\Report\ReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request)
    {
        if (!$request->hasAny(['ta', 'students', 'materials'])) {
            return view('reports.index');
        }

        $types = ResourceType::orderBy('name')->get();
        $deficiencies = Deficiency::orderBy('name')->get();
        $accessibilityFeatures = AccessibilityFeature::orderBy('name')->get();

        $report = $this->reportService->generate($request);
        $data = $report['data'];

        return view('reports.configure', compact('data', 'types', 'deficiencies', 'accessibilityFeatures'));
    }

    public function configure(Request $request)
    {
        if (!$request->hasAny(['ta', 'students', 'materials'])) {
            return redirect()->route('report.reports.index');
        }

        $types = ResourceType::orderBy('name')->get();
        $deficiencies = Deficiency::orderBy('name')->get();
        $accessibilityFeatures = AccessibilityFeature::orderBy('name')->get();

        $report = $this->reportService->generate($request);
        $data = $report['data'];

        return view('reports.configure', compact('data', 'types', 'deficiencies', 'accessibilityFeatures'));
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        $report = $this->reportService->generate($request);
        $data = $report['data'];

        // Garantindo filtros por módulo
        $moduleFilters = $report['filters'] ?? [];

        // Cada módulo recebe só seus filtros (TA ou MPA)
        foreach ($moduleFilters as $module => $filters) {
            if (!empty($filters)) {
                $moduleFilters[$module] = $filters; // já está ok, só garantindo array
            } else {
                $moduleFilters[$module] = []; // evita null
            }
        }

        $pdf = Pdf::loadView('reports.pdf', compact('data', 'moduleFilters'))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'enable_php' => true,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

        return $pdf->stream("Relatorio_Radar_" . now()->format('d_m_Y') . ".pdf");
    }


    public function exportExcel(Request $request)
    {
        $report = $this->reportService->generate($request);

        $hasData = collect($report['data'])->contains(fn($module) => count($module) > 0);
        if (!$hasData) {
            return redirect()->back()->with('error', 'Nenhum dado encontrado para exportar com esses filtros.');
        }

        // Passa filtros isolados por módulo para cada aba
        $excelData = [];
        foreach ($report['data'] as $module => $items) {
            $moduleFilters = $report['filters'][$module] ?? [];
            $filterText = !empty($moduleFilters) ? implode(' | ', $moduleFilters) : 'Sem filtros';
            $excelData[$module] = [
                'items' => $items,
                'filterText' => $filterText
            ];
        }

        return Excel::download(
            new ReportExport($excelData),
            'Relatorio_Recursos_' . now()->format('d_m_Y') . '.xlsx'
        );
    }


    public function exportHtml(Request $request)
    {
        $report = $this->reportService->generate($request);
        $data = $report['data'];
        return view('reports.exports.html', compact('data'));
    }
}
