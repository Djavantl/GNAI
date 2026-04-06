<?php

namespace App\Http\Controllers\InclusiveRadar;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AssistiveTechnologyRequest;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\Inspection;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\InclusiveRadar\AssistiveTechnologyService;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssistiveTechnologyController extends Controller
{
    public function __construct(
        private AssistiveTechnologyService $service
    ) {}

    public function index(Request $request): View
    {
        $name = trim($request->name ?? '');

        $query = AssistiveTechnology::with([
            'deficiencies'
        ])
            ->filterName($name ?: null)
            ->active($request->is_active)
            ->digital($request->is_digital);

        if ($request->filled('status')) {
            $status = ResourceStatus::tryFrom($request->status);
            if ($status) {
                $query->where('status', $status->value);
            }
        }

        $assistiveTechnologies = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.assistive-technologies.partials.table',
                compact('assistiveTechnologies')
            );
        }

        return view(
            'pages.inclusive-radar.assistive-technologies.index',
            compact('assistiveTechnologies')
        );
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.assistive-technologies.create', [

            'deficiencies' => Deficiency::orderBy('name')->get(),

            'inspectionTypes' => collect(InspectionType::cases())
                ->filter(fn($item) => $item !== InspectionType::MAINTENANCE)
                ->mapWithKeys(fn($item) => [$item->value => $item->label()]),
            'defaultInspection' => InspectionType::INITIAL->value,

            'resourceStatuses' => collect(ResourceStatus::cases())
                ->mapWithKeys(fn($i) => [$i->value => $i->label()]),
            'defaultStatus' => ResourceStatus::AVAILABLE->value,

            'conservationStates' => collect(ConservationState::cases())
                ->mapWithKeys(fn($item) => [$item->value => $item->label()]),
        ]);
    }

    public function store(AssistiveTechnologyRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva criada com sucesso!');
    }

    public function show(AssistiveTechnology $assistiveTechnology): View
    {
        $assistiveTechnology->load([
            'deficiencies' => fn($q) => $q->orderBy('name'),
            'inspections' => fn($q) => $q->with('images')->latest('inspection_date'),
            'loans',
        ]);

        return view('pages.inclusive-radar.assistive-technologies.show', [
            'assistiveTechnology' => $assistiveTechnology,
            'deficiencies'        => $assistiveTechnology->deficiencies,
            'inspections'         => $assistiveTechnology->inspections,
        ]);
    }

    public function edit(AssistiveTechnology $assistiveTechnology): View
    {
        $assistiveTechnology->load(['deficiencies', 'inspections.images']);

        return view('pages.inclusive-radar.assistive-technologies.edit', [
            'assistiveTechnology' => $assistiveTechnology,

            'resourceStatuses' => collect(ResourceStatus::cases())
                ->mapWithKeys(fn($i) => [$i->value => $i->label()]),

            'conservationStates' => collect(ConservationState::cases())
                ->mapWithKeys(fn($i) => [$i->value => $i->label()]),

            'inspectionTypes' => collect(InspectionType::cases())
                ->mapWithKeys(fn($item) => [$item->value => $item->label()]),
            'defaultInspection' => InspectionType::PERIODIC->value,

            'deficiencies' => Deficiency::orderBy('name')->get(),

            'activeLoans' => $assistiveTechnology->loans()->whereIn('status', ['active', 'late'])->count(),
        ]);
    }

    public function update(AssistiveTechnologyRequest $request, AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->update($assistiveTechnology, $request->validated());

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva atualizada com sucesso!');
    }

    public function destroy(AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->delete($assistiveTechnology);

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia removida com sucesso!');
    }

    public function generatePdf(AssistiveTechnology $assistiveTechnology): Response
    {
        $assistiveTechnology->load([
            'deficiencies',
            'inspections.images',
        ]);

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.assistive-technologies.pdf',
            compact('assistiveTechnology')
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

        return $pdf->stream("TA_{$assistiveTechnology->name}.pdf");
    }

    public function showInspection(AssistiveTechnology $assistiveTechnology, Inspection $inspection): View
    {
        abort_if(
            $inspection->inspectable_id !== $assistiveTechnology->id ||
            $inspection->inspectable_type !== $assistiveTechnology->getMorphClass(),
            403
        );

        $inspection->load(['images', 'inspectable']);

        return view('pages.inclusive-radar.assistive-technologies.inspections.show', [
            'assistiveTechnology' => $assistiveTechnology,
            'inspection'          => $inspection,
        ]);
    }
}
