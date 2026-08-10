<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Application\Actions;

use App\Domains\Reporting\Application\Data\RunReportData;
use App\Domains\Reporting\Application\Services\ReportCatalog;
use App\Domains\Reporting\Domain\Exceptions\ReportingException;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Symfony\Component\HttpFoundation\Response;

final readonly class ExportReportPdfAction
{
    public function __construct(
        private RunReportAction $runReport,
        private ReportCatalog $catalog,
    ) {}

    /**
     * @throws ReportingException
     * @throws Exception
     */
    public function execute(RunReportData $data): Response
    {
        $result = $this->runReport->execute(data: $data, limit: 1000);
        $source = $this->catalog->get($data->source);
        $pdf = Pdf::loadView('reports.pdf', [
            'data' => $result->rows,
            'headers' => array_values($result->headers),
            'subject' => $source->label(),
        ]);

        PdfPageNumberer::apply($pdf);

        return $pdf->download('relatorio-'.str($source->label())->slug().'.pdf');
    }
}
