<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\MaintenanceStageRequest;
use App\Services\InclusiveRadar\MaintenanceService;
use App\Models\InclusiveRadar\Maintenance;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function __construct(protected MaintenanceService $maintenanceService)
    {
    }

    // ===================== DASHBOARD =====================
    public function index(Request $request)
    {
        $resources = $this->maintenanceService->maintenanceDashboardResources($request->all());

        if ($request->ajax()) {
            return view('pages.inclusive-radar.maintenances.partials.table', compact('resources'));
        }

        return view('pages.inclusive-radar.maintenances.index', compact('resources'));
    }

    // ===================== STEP 0 =====================
    // Recebe tipo + id para suportar TA ou MPA
    public function openMaintenanceRequest(string $type, int $id)
    {
        // Descobre o modelo baseado no tipo
        $resource = match($type) {
            'ta'  => AssistiveTechnology::findOrFail($id),
            'mpa' => AccessibleEducationalMaterial::findOrFail($id),
            default => abort(404, 'Tipo de recurso inválido'),
        };

        $maintenance = $this->maintenanceService->openMaintenanceRequest($resource);

        return redirect()
            ->route('inclusive-radar.maintenances.show', $maintenance)
            ->with('success', 'Manutenção criada com sucesso! Agora você pode iniciar as etapas.');
    }

    // ===================== STEP 1 =====================
    public function stage1(Maintenance $maintenance)
    {
        $maintenance->load('stages.user', 'stages.starter', 'maintainable');
        $stage1 = $maintenance->stages->firstWhere('step_number', 1);

        return view('pages.inclusive-radar.maintenances.stage1', compact('maintenance', 'stage1'));
    }

    public function saveStage1(MaintenanceStageRequest $request, Maintenance $maintenance)
    {
        $userId = auth()->id();
        $finalize = (bool) $request->input('finalize');

        $this->maintenanceService->saveStage1(
            $maintenance,
            $request->validated(),
            $userId,
            $finalize
        );

        if ($finalize) {
            return redirect()->route('inclusive-radar.maintenances.stage2', $maintenance)
                ->with('success', 'Etapa 1 concluída com sucesso! Prossiga com a finalização.');
        }

        return redirect()->route('inclusive-radar.maintenances.stage1', $maintenance)
            ->with('success', 'Rascunho da Etapa 1 salvo.');
    }

    // ===================== STEP 2 =====================
    public function stage2(Maintenance $maintenance)
    {
        $maintenance->load('stages.user', 'stages.starter', 'maintainable');
        $stage2 = $maintenance->stages->firstWhere('step_number', 2);
        $stage1 = $maintenance->stages->firstWhere('step_number', 1);

        return view('pages.inclusive-radar.maintenances.stage2', compact('maintenance', 'stage1', 'stage2'));
    }

    public function saveStage2(MaintenanceStageRequest $request, Maintenance $maintenance)
    {
        $userId = auth()->id();
        $finalize = $request->input('finalize') == '1';

        $data = $request->validated();
        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        $stage2 = $this->maintenanceService->saveStage2(
            $maintenance,
            $data,
            $userId,
            $finalize
        );

        $route = $finalize
            ? route('inclusive-radar.maintenances.show', $maintenance)
            : route('inclusive-radar.maintenances.stage2', $maintenance);

        return redirect($route)->with('success', $finalize ? 'Manutenção concluída e vistoria registrada!' : 'Rascunho da etapa 2 salvo.');
    }

    // ===================== SHOW =====================
    public function show(Maintenance $maintenance)
    {
        $maintenance->load('stages.user', 'stages.starter', 'maintainable');
        return view('pages.inclusive-radar.maintenances.show', compact('maintenance'));
    }

    public function generatePdf(Maintenance $maintenance)
    {
        $maintenance->load([
            'maintainable.inspections.images',
            'stages.user',
            'stages.starter',
        ]);

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.maintenances.pdf',
            compact('maintenance')
        )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'enable_php' => true,
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => [storage_path('app/public'), public_path()],
            ]);

        return $pdf->stream("Relatorio_Manutencao_{$maintenance->id}.pdf");
    }
}
