<?php

namespace App\Exports;

use App\Exports\InclusiveRadar\Reports\AccessibleEducationalReportExport;
use App\Exports\InclusiveRadar\Reports\AssistiveTechnologyReportExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportExport implements WithMultipleSheets
{
    protected array $data;

    /**
     * $data deve ser um array associativo por módulo:
     * [
     *   'ta' => ['items' => ..., 'filterText' => ...],
     *   'materials' => ['items' => ..., 'filterText' => ...]
     * ]
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Aba de Tecnologias Assistivas
        if (!empty($this->data['ta'])) {
            $items = $this->data['ta']['items'] ?? [];
            $filterText = $this->data['ta']['filterText'] ?? 'Sem filtros';
            $sheets[] = new AssistiveTechnologyReportExport($items, $filterText);
        }

        // Aba de Materiais Pedagógicos Acessíveis
        if (!empty($this->data['materials'])) {
            $items = $this->data['materials']['items'] ?? [];
            $filterText = $this->data['materials']['filterText'] ?? 'Sem filtros';
            $sheets[] = new AccessibleEducationalReportExport($items, $filterText);
        }

        return $sheets;
    }
}
