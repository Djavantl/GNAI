<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\UI\Controllers;

use App\Domains\InclusiveRadar\Application\Actions\InstitutionalEvents\CreateInstitutionalEventAction;
use App\Domains\InclusiveRadar\Application\Actions\InstitutionalEvents\DeleteInstitutionalEventAction;
use App\Domains\InclusiveRadar\Application\Actions\InstitutionalEvents\UpdateInstitutionalEventAction;
use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\CreateInstitutionalEventData;
use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\ListInstitutionalEventsData;
use App\Domains\InclusiveRadar\Application\Data\InstitutionalEvents\UpdateInstitutionalEventData;
use App\Domains\InclusiveRadar\Application\Queries\InstitutionalEvents\InstitutionalEventPdfQuery;
use App\Domains\InclusiveRadar\Application\Queries\InstitutionalEvents\ListInstitutionalEventsQuery;
use App\Domains\InclusiveRadar\Application\Queries\InstitutionalEvents\ShowInstitutionalEventQuery;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitutionalEvent;
use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent;
use App\Http\Controllers\Controller;
use App\Support\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

final class InstitutionalEventController extends Controller
{
    public function index(ListInstitutionalEventsData $filters, ListInstitutionalEventsQuery $query, Request $request): View
    {
        $events = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.institutional-events.partials.table',
                compact('events'),
            );
        }

        return view(
            'pages.inclusive-radar.institutional-events.index',
            compact('events'),
        );
    }

    public function create(Request $request): View
    {
        $backRoute = $request->query('back')
            ?? route('inclusive-radar.institutional-events.index');

        return view('pages.inclusive-radar.institutional-events.create', compact('backRoute'));
    }

    /**
     * @throws InvalidInstitutionalEvent
     */
    public function store(CreateInstitutionalEventData $data, CreateInstitutionalEventAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('inclusive-radar.institutional-events.index')
            ->with('success', 'Evento criado com sucesso!');
    }

    public function show(Request $request, InstitutionalEvent $event, ShowInstitutionalEventQuery $query): View
    {
        $event = $query->execute($event);
        $backRoute = $request->query('back')
            ?? route('inclusive-radar.institutional-events.index');

        return view('pages.inclusive-radar.institutional-events.show', compact('event', 'backRoute'));
    }

    public function edit(InstitutionalEvent $event): View
    {
        return view('pages.inclusive-radar.institutional-events.edit', compact('event'));
    }

    /**
     * @throws InvalidInstitutionalEvent
     */
    public function update(UpdateInstitutionalEventData $data, InstitutionalEvent $event, UpdateInstitutionalEventAction $action): RedirectResponse
    {
        $action->execute($event, $data);

        return redirect()
            ->route('inclusive-radar.institutional-events.index')
            ->with('success', 'Evento atualizado com sucesso!');
    }

    /**
     * @throws \Throwable
     */
    public function destroy(InstitutionalEvent $event, DeleteInstitutionalEventAction $action): RedirectResponse
    {
        $action->execute($event);

        return redirect()
            ->route('inclusive-radar.institutional-events.index')
            ->with('success', 'Evento removido com sucesso!');
    }

    public function generatePdf(InstitutionalEvent $event, InstitutionalEventPdfQuery $query): Response
    {
        $event = $query->execute($event);

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.institutional-events.pdf',
            compact('event'),
        )
            ->setPaper('a4', 'portrait');

        PdfPageNumberer::apply($pdf);

        return $pdf->stream("Evento_{$event->id}.pdf");
    }
}
