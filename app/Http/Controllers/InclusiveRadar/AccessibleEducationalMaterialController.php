<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AccessibleEducationalMaterialRequest;
use App\Models\InclusiveRadar\AccessibilityFeature;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\Inspection;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Services\InclusiveRadar\AccessibleEducationalMaterialService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccessibleEducationalMaterialController extends Controller
{
    public function __construct(
        protected AccessibleEducationalMaterialService $service
    ) {}

    public function index(Request $request): View
    {
        $name = trim($request->name ?? '');

        $query = AccessibleEducationalMaterial::with([
            'deficiencies',
            'accessibilityFeatures',
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

        if ($request->filled('available')) {
            $query->where('status', ResourceStatus::AVAILABLE->value);
        }

        $materials = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.accessible-educational-materials.partials.table',
                compact('materials')
            );
        }

        return view(
            'pages.inclusive-radar.accessible-educational-materials.index',
            compact('materials')
        );
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.accessible-educational-materials.create', [

            'accessibilityFeatures' => AccessibilityFeature::where('is_active', true)->orderBy('name')->get(),

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

    public function store(AccessibleEducationalMaterialRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material criado com sucesso!');
    }

    public function show(AccessibleEducationalMaterial $material): View
    {
        $material->load([
            'deficiencies' => fn($q) => $q->orderBy('name'),
            'accessibilityFeatures' => fn($q) => $q->orderBy('name'),
            'inspections' => fn($q) => $q->with('images')->latest('inspection_date'),
            'loans',
        ]);

        return view('pages.inclusive-radar.accessible-educational-materials.show', [
            'material'       => $material,
            'deficiencies'   => $material->deficiencies,
            'features'       => $material->accessibilityFeatures,
            'inspections'    => $material->inspections,
        ]);
    }

    public function edit(AccessibleEducationalMaterial $material): View
    {
        $material->load(['deficiencies', 'accessibilityFeatures', 'inspections.images']);

        return view('pages.inclusive-radar.accessible-educational-materials.edit', [
            'material' => $material,

            'resourceStatuses' => collect(ResourceStatus::cases())
                ->mapWithKeys(fn($i) => [$i->value => $i->label()]),

            'conservationStates' => collect(ConservationState::cases())
                ->mapWithKeys(fn($i) => [$i->value => $i->label()]),

            'inspectionTypes' => collect(InspectionType::cases())
                ->mapWithKeys(fn($item) => [$item->value => $item->label()]),
            'defaultInspection' => InspectionType::PERIODIC->value,

            'accessibilityFeatures' => AccessibilityFeature::where('is_active', true)->orderBy('name')->get(),
            'deficiencies' => Deficiency::orderBy('name')->get(),

            'activeLoans' => $material->loans()->whereIn('status', ['active', 'late'])->count(),
        ]);
    }

    public function update(AccessibleEducationalMaterialRequest $request, AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->update($material, $request->validated());

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material atualizado com sucesso!');
    }

    public function destroy(AccessibleEducationalMaterial $material): RedirectResponse
    {
        $this->service->delete($material);

        return redirect()
            ->route('inclusive-radar.accessible-educational-materials.index')
            ->with('success', 'Material removido!');
    }

    public function generatePdf(AccessibleEducationalMaterial $material)
    {
        $material->load([
            'deficiencies',
            'accessibilityFeatures',
            'inspections.images',
        ]);

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.accessible-educational-materials.pdf',
            compact('material')
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

        return $pdf->stream("MPA_{$material->name}.pdf");
    }

    public function showInspection(AccessibleEducationalMaterial $material, Inspection $inspection): View
    {
        abort_if(
            $inspection->inspectable_id !== $material->id ||
            $inspection->inspectable_type !== $material->getMorphClass(),
            403
        );

        $inspection->load(['images', 'inspectable']);

        return view('pages.inclusive-radar.accessible-educational-materials.inspections.show', [
            'material'   => $material,
            'inspection' => $inspection,
        ]);
    }
}
