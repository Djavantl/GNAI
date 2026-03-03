<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Enums\InclusiveRadar\ResourceStatus;
use App\Exports\InclusiveRadar\Items\AssistiveTechnologyExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\AssistiveTechnologyRequest;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\Inspection;
use App\Services\InclusiveRadar\AssistiveTechnologyService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AssistiveTechnologyController extends Controller
{
    public function __construct(
        protected AssistiveTechnologyService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | LISTAGEM
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $name = trim($request->name ?? '');

        $query = AssistiveTechnology::with([
            'deficiencies'
        ])
            ->withCount('trainings')
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

    /*
    |--------------------------------------------------------------------------
    | CRIAÇÃO
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        return view('pages.inclusive-radar.assistive-technologies.create', [
            'statuses' => ResourceStatus::cases(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(AssistiveTechnologyRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva criada com sucesso!');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(AssistiveTechnology $assistiveTechnology): View
    {
        $assistiveTechnology->load([
            'deficiencies',
            'inspections.images',
            'loans',
            'trainings',
        ]);

        return view(
            'pages.inclusive-radar.assistive-technologies.show',
            compact('assistiveTechnology')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(AssistiveTechnology $assistiveTechnology): View
    {
        $assistiveTechnology->load([
            'deficiencies',
            'inspections.images',
            'trainings',
        ]);

        return view(
            'pages.inclusive-radar.assistive-technologies.edit',
            [
                'assistiveTechnology' => $assistiveTechnology,
                'statuses' => ResourceStatus::cases(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(AssistiveTechnologyRequest $request, AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->update($assistiveTechnology, $request->validated());

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva atualizada com sucesso!');
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE ACTIVE
    |--------------------------------------------------------------------------
    */

    public function toggleActive(AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->toggleActive($assistiveTechnology);

        return back()->with('success', 'Status atualizado com sucesso!');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->delete($assistiveTechnology);

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia removida com sucesso!');
    }

    /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

    public function generatePdf(AssistiveTechnology $assistiveTechnology)
    {
        $assistiveTechnology->load([
            'deficiencies',
            'inspections.images',
            'trainings',
        ]);

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.assistive-technologies.pdf',
            compact('assistiveTechnology')
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

        return $pdf->stream("TA_{$assistiveTechnology->name}.pdf");
    }

    /*
    |--------------------------------------------------------------------------
    | EXCEL
    |--------------------------------------------------------------------------
    */

    public function exportExcel(AssistiveTechnology $assistiveTechnology)
    {
        return Excel::download(
            new AssistiveTechnologyExport(
                collect([$assistiveTechnology]),
                $assistiveTechnology->name,
                $assistiveTechnology->status->label() // 🔥 usando enum
            ),
            'TA_'.$assistiveTechnology->name.'.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | INSPEÇÃO
    |--------------------------------------------------------------------------
    */

    public function showInspection(AssistiveTechnology $assistiveTechnology, Inspection $inspection)
    {
        abort_if(
            $inspection->inspectable_id !== $assistiveTechnology->id ||
            $inspection->inspectable_type !== $assistiveTechnology->getMorphClass(),
            403
        );

        $inspection->load('images', 'inspectable');

        return view(
            'pages.inclusive-radar.assistive-technologies.inspections.show',
            compact('assistiveTechnology', 'inspection')
        );
    }
}
