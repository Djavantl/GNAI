<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\BarrierRequest;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\BarrierCategory;
use App\Models\InclusiveRadar\Inspection;
use App\Models\InclusiveRadar\Institution;
use App\Models\InclusiveRadar\Location;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use App\Services\InclusiveRadar\BarrierService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Exception;

class BarrierController extends Controller
{
    public function __construct(
        protected BarrierService $service
    )
    {}

    /**
     * Dashboard e Listagem Principal
     */
    public function index(Request $request): View
    {
        $barriers = Barrier::with([
            'category',
            'institution',
            'inspections'
        ])
            ->name($request->name)
            ->category($request->category)
            ->priority($request->priority)

            ->when($request->status, function ($query) use ($request) {

                $query->whereHas('inspections', function ($sub) use ($request) {
                    $sub->where('status', $request->status)
                        ->whereRaw('inspection_date = (
                        select max(inspection_date)
                        from inspections
                        where inspectable_id = barriers.id
                        and inspectable_type = ?
                    )', [Barrier::class]);
                });

            })

            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('pages.inclusive-radar.barriers.partials.table', compact('barriers'));
        }

        return view('pages.inclusive-radar.barriers.index', compact('barriers'));
    }

    /**
     * ETAPA 1 – Formulário de Identificação
     */
    public function create(): View
    {
        $institutions = Institution::with('locations')->orderBy('name')->get();
        $categories = BarrierCategory::orderBy('name')->get();
        $deficiencies = Deficiency::orderBy('name')->get();
        $students = Student::with('person')->get();
        $professionals = Professional::with('person')->get();

        $selectedInstitution = old('institution_id')
            ? $institutions->firstWhere('id', old('institution_id'))
            : null;

        $barrier = new Barrier();

        return view('pages.inclusive-radar.barriers.stage1', compact(
            'institutions',
            'categories',
            'deficiencies',
            'students',
            'professionals',
            'barrier',
            'selectedInstitution'
        ));
    }

    /**
     * ETAPA 1 – Salvar Identificação Inicial
     */
    public function store(BarrierRequest $request): RedirectResponse
    {
        try {
            $this->service->storeStage1($request->validated(), Auth::id());

            return redirect()
                ->route('inclusive-radar.barriers.index')
                ->with('success', 'Barreira identificada e registrada com sucesso!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Erro ao salvar: ' . $e->getMessage());
        }
    }

    public function stage1(Barrier $barrier): View
    {
        $institutions = Institution::orderBy('name')->get();
        $categories = BarrierCategory::orderBy('name')->get();
        $deficiencies = Deficiency::orderBy('name')->get();
        $students = Student::with('person')->get();
        $professionals = Professional::with('person')->get();

        return view('pages.inclusive-radar.barriers.stage1', compact(
            'institutions',
            'categories',
            'deficiencies',
            'students',
            'professionals',
            'barrier'
        ));
    }

    /**
     * Exibição Detalhada (Timeline/Show)
     */
    public function show(Barrier $barrier): View
    {
        $barrier->load([
            'category',
            'institution',
            'location',
            'deficiencies',
            'inspections.images',
        ]);

        return view('pages.inclusive-radar.barriers.show', compact('barrier'));
    }

    /**
     * ETAPA 2 – Mostrar Formulário de Análise
     */
    public function stage2(Barrier $barrier): View
    {
        $deficiencies = Deficiency::all();

        $barrier->load(['deficiencies', 'inspections.images']);
        return view('pages.inclusive-radar.barriers.stage2', compact('barrier', 'deficiencies'));
    }

    /**
     * ETAPA 2 – Salvar Análise
     */
    public function saveStage2(Barrier $barrier, BarrierRequest $request): RedirectResponse
    {

        try {
            $this->service->storeStage2($barrier, $request->validated(), Auth::id());
            return redirect()->route('inclusive-radar.barriers.index')
                ->with('success', 'Análise técnica concluída com sucesso.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * ETAPA 3 – Mostrar Formulário de Tratamento
     */
    public function stage3(Barrier $barrier): View
    {
        return view('pages.inclusive-radar.barriers.stage3', compact('barrier'));
    }

    /**
     * ETAPA 3 – Salvar Plano de Tratamento
     */
    public function saveStage3(Barrier $barrier, BarrierRequest $request): RedirectResponse
    {
        try {
            $this->service->storeStage3($barrier, $request->validated(), Auth::id());
            return redirect()->route('inclusive-radar.barriers.index')
                ->with('success', 'Plano de tratamento iniciado.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * ETAPA 4 – Mostrar Formulário de Resolução
     */
    public function stage4(Barrier $barrier): View
    {
        return view('pages.inclusive-radar.barriers.stage4', compact('barrier'));
    }

    /**
     * ETAPA 4 – Salvar Resolução e Encerrar
     */
    public function saveStage4(Barrier $barrier, BarrierRequest $request): RedirectResponse
    {
        try {
            $this->service->storeStage4($barrier, $request->validated(), Auth::id());
            return redirect()->route('inclusive-radar.barriers.index')
                ->with('success', 'Barreira resolvida e validada com sucesso!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Utilitário: Remoção
     */
    public function destroy(Barrier $barrier): RedirectResponse
    {
        try {
            $barrier->delete();
            return redirect()->route('inclusive-radar.barriers.index')
                ->with('success', 'Registro de barreira removido.');
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao remover: ' . $e->getMessage());
        }
    }

    /**
     * Exibe os detalhes de uma inspeção específica vinculada à barreira
     */
    public function showInspection(Barrier $barrier, Inspection $inspection): View
    {
        abort_if(
            $inspection->inspectable_id !== $barrier->id ||
            $inspection->inspectable_type !== 'barrier',
            403
        );

        $inspection->load(['images', 'inspectable']);

        return view('pages.inclusive-radar.barriers.inspections.show', compact('barrier', 'inspection'));
    }
}
