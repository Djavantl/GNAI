<?php

namespace App\Http\Controllers\InclusiveRadar;

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

        $assistiveTechnologies = AssistiveTechnology::with([
            'resourceStatus',
            'deficiencies'
        ])
            ->withCount('trainings')
            ->filterName($name ?: null)
            ->active($request->is_active)
            ->digital($request->is_digital)
            ->available($request->available)
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
    | FORMULÁRIO DE CRIAÇÃO
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        return view('pages.inclusive-radar.assistive-technologies.create');
    }

    /*
    |--------------------------------------------------------------------------
    | ARMAZENAMENTO
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
    | VISUALIZAÇÃO
    |--------------------------------------------------------------------------
    */

    public function show(AssistiveTechnology $assistiveTechnology): View
    {
        $assistiveTechnology->load([
            'resourceStatus',
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
    | FORMULÁRIO DE EDIÇÃO
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
            compact('assistiveTechnology')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ATUALIZAÇÃO
    |--------------------------------------------------------------------------
    */

    public function update(
        AssistiveTechnologyRequest $request,
        AssistiveTechnology $assistiveTechnology
    ): RedirectResponse {
        $this->service->update($assistiveTechnology, $request->validated());

        return redirect()
            ->route('inclusive-radar.assistive-technologies.index')
            ->with('success', 'Tecnologia assistiva atualizada com sucesso!');
    }

    /*
    |--------------------------------------------------------------------------
    | ATIVAR / DESATIVAR
    |--------------------------------------------------------------------------
    */

    public function toggleActive(AssistiveTechnology $assistiveTechnology): RedirectResponse
    {
        $this->service->toggleActive($assistiveTechnology);

        return redirect()
            ->back()
            ->with('success', 'Status atualizado com sucesso!');
    }

    /*
    |--------------------------------------------------------------------------
    | EXCLUSÃO
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
    | GERAÇÃO DE PDF
    |--------------------------------------------------------------------------
    */

    public function generatePdf(AssistiveTechnology $assistiveTechnology)
    {
        $assistiveTechnology->load([
            'resourceStatus',
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

        return $pdf->stream("TA_{$assistiveTechnology->type}.pdf");
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORTAÇÃO EXCEL
    |--------------------------------------------------------------------------
    */

    public function exportExcel(AssistiveTechnology $assistiveTechnology)
    {
        $assistiveTechnology->load([
            'deficiencies',
            'inspections.images'
        ]);

        return Excel::download(
            new AssistiveTechnologyExport(
                collect([$assistiveTechnology]),
                $assistiveTechnology->type,
                $assistiveTechnology->is_active ? 'Ativo' : 'Inativo'
            ),
            'TA_'.$assistiveTechnology->type.'.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VISUALIZAÇÃO DE INSPEÇÃO
    |--------------------------------------------------------------------------
    */

    public function showInspection(
        AssistiveTechnology $assistiveTechnology,
        Inspection $inspection
    ) {
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
