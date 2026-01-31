<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function generate(Request $request, ReportService $reportService)
    {
        return $reportService->generate(
            modules: $request->input('modules', []),
            filters: [],
            layout: [
                'title' => $request->input('header_title'),
                'subtitle' => $request->input('header_subtitle'),
            ],
            format: $request->input('format', 'screen')
        );
    }
}
