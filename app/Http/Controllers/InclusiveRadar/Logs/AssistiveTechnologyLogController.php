<?php

namespace App\Http\Controllers\InclusiveRadar\Logs;

use App\Http\Controllers\Controller;
use App\Models\InclusiveRadar\AssistiveTechnology;
use Barryvdh\DomPDF\Facade\Pdf;

class AssistiveTechnologyLogController extends Controller
{
    public function index(AssistiveTechnology $assistiveTechnology)
    {
        $logs = $assistiveTechnology->logs()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view(
            'pages.inclusive-radar.assistive-technologies.logs.logs',
            compact('assistiveTechnology', 'logs')
        );
    }

    public function generatePdf(AssistiveTechnology $assistiveTechnology)
    {
        $logs = $assistiveTechnology->logs()
            ->with('user')
            ->latest()
            ->get();

        $fieldLabels = [
            'name' => 'Nome',
            'description' => 'Descrição',
            'status_id' => 'Status',
            'is_active' => 'Ativo',
            'type_id' => 'Tipo',
            'quantity' => 'Quantidade',
            'quantity_available' => 'Quantidade disponível',
            'requires_training' => 'Requer treinamento',
            'notes' => 'Notas',
            'conservation_state' => 'Conservação',
        ];

        $pdf = Pdf::loadView(
            'pages.inclusive-radar.assistive-technologies.logs.logs-pdf',
            compact('assistiveTechnology', 'logs', 'fieldLabels')
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true]);

        return $pdf->stream("Histórico_{$assistiveTechnology->name}.pdf");
    }
}
