<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Concerns\ResolvesBackRoute;
use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\InstitutionalEventRequest;
use App\Models\InclusiveRadar\InstitutionalEvent;
use App\Services\InclusiveRadar\InstitutionalEventService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstitutionalEventController extends Controller
{
    use ResolvesBackRoute;

    public function __construct(
        protected InstitutionalEventService $service
    ) {}

    public function index(Request $request): View
    {
        $title = trim($request->title ?? '');

        $events = InstitutionalEvent::query()
            ->searchTitle($title ?: null)
            ->when($request->filled('is_active'), fn($query) => $query->active($request->is_active))
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('pages.inclusive-radar.institutional-events.partials.table', compact('events'));
        }

        return view('pages.inclusive-radar.institutional-events.index', compact('events'));
    }

    public function create(Request $request): View
    {
        $backRoute = $this->resolveBackRoute($request, 'inclusive-radar.institutional-events.index');

        return view('pages.inclusive-radar.institutional-events.create', compact('backRoute'));
    }

    public function store(InstitutionalEventRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.institutional-events.index')
            ->with('success', 'Evento criado com sucesso!');
    }

    public function show(Request $request, InstitutionalEvent $event): View
    {
        $backRoute = $this->resolveBackRoute($request, 'inclusive-radar.institutional-events.index');

        return view('pages.inclusive-radar.institutional-events.show', compact('event', 'backRoute'));
    }

    public function edit(Request $request, InstitutionalEvent $event): View
    {
        return view('pages.inclusive-radar.institutional-events.edit', compact('event'));
    }

    public function update(InstitutionalEventRequest $request, InstitutionalEvent $event): RedirectResponse
    {
        $this->service->update($event, $request->validated());

        return redirect()
            ->route('inclusive-radar.institutional-events.index')
            ->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(InstitutionalEvent $event): RedirectResponse
    {
        try {
            $this->service->delete($event);

            return redirect()
                ->route('inclusive-radar.institutional-events.index')
                ->with('success', 'Evento removido com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
