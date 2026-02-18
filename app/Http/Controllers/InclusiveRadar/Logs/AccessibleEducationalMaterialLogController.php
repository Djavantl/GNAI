<?php

namespace App\Http\Controllers\InclusiveRadar\Logs;

use App\Http\Controllers\Controller;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;

class AccessibleEducationalMaterialLogController extends Controller
{
    /**
     * Exibe a listagem de logs do MPA.
     */
    public function index(AccessibleEducationalMaterial $material): View
    {
        $logs = $material->logs()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view(
            'pages.inclusive-radar.accessible-educational-materials.logs.logs',
            compact('material', 'logs')
        );
    }

    /**
     * Gera o PDF com o histórico de alterações do MPA.
     */
    public function generatePdf(AccessibleEducationalMaterial $material)
    {
        $logs = $material->logs()
            ->with('user')
            ->latest()
            ->get();

        // Labels específicos para os campos do MPA
        $fieldLabels = [
            'name'                   => 'Nome do Material',
            'type_id'                => 'Tipo',
            'asset_code'             => 'Código de Patrimônio',
            'quantity'               => 'Quantidade Total',
            'quantity_available'     => 'Qtd Disponível',
            'conservation_state'     => 'Estado de Conservação',
            'status_id'              => 'Status',
            'is_active'              => 'Ativo',
            'notes'                  => 'Notas',
            'deficiencies'           => 'Público-Alvo',
            'accessibility_features' => 'Recursos de Acessibilidade',
            'trainings'              => 'Treinamentos',
            'attributes'             => 'Características Técnicas',
        ];

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.accessible-educational-materials.logs.logs-pdf',
            compact('material', 'logs', 'fieldLabels')
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

        return $pdf->stream("Historico_MPA_{$material->name}.pdf");
    }
}
