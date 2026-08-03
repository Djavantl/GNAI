<?php

declare(strict_types=1);

namespace App\Domains\Reporting\UI\Controllers;

use App\Domains\Reporting\Application\Actions\ExportReportPdfAction;
use App\Domains\Reporting\Application\Actions\RunReportAction;
use App\Domains\Reporting\Application\Data\RunReportData;
use App\Domains\Reporting\Application\Queries\GetReportMetadataQuery;
use App\Domains\Reporting\Application\Queries\ListReportSourcesQuery;
use App\Domains\Reporting\Domain\Exceptions\ReportingException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class ReportController
{
    public function index(): View
    {
        return view('reports.builder');
    }

    public function sources(ListReportSourcesQuery $query): JsonResponse
    {
        return response()->json($query->execute());
    }

    public function metadata(Request $request, GetReportMetadataQuery $query): JsonResponse
    {
        $validated = $request->validate([
            'source' => ['required', 'string', 'regex:/^[a-z0-9.-]+$/', 'max:100'],
        ]);

        return response()->json($query->execute($validated['source']));
    }

    /**
     * @throws ReportingException
     */
    public function run(RunReportData $data, RunReportAction $action): JsonResponse
    {
        $result = $action->execute($data);

        return response()->json($result->toArray());
    }

    /**
     * @throws ReportingException
     */
    public function exportPdf(RunReportData $data, ExportReportPdfAction $action): Response
    {
        return $action->execute($data);
    }
}
