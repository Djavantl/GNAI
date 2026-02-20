<?php

namespace App\Exports\InclusiveRadar\Reports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssistiveTechnologyReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithEvents,
    WithCustomStartCell,
    WithTitle
{
    protected Collection $items;
    protected string $filterText;

    public function __construct($items, string $filterText = '')
    {
        $this->items = collect($items);
        $this->filterText = $filterText;
    }

    public function title(): string
    {
        return 'Tecnologias Assistivas';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome do Recurso',
            'Tipo / Categoria',
            'Cód. Patrimônio',
            'Qtd. Total',
            'Qtd. Disponível',
            'Estado de Conservação',
            'Status',
            'Requer Treinamento',
            'Formato',
            'Deficiências'
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->name,
            $item->type?->name ?? 'N/A',
            $item->asset_code ?? 'Sem Código',
            $item->quantity,
            $item->quantity_available,
            $item->conservation_state?->label() ?? $item->conservation_state,
            $item->is_active ? 'Ativo' : 'Inativo',
            $item->requires_training ? 'Sim' : 'Não',
            $item->type?->is_digital ? 'Digital' : 'Físico',
            $item->deficiencies->pluck('name')->implode(', ')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $totalItems = $this->items->count();

                // Ajustar largura do cabeçalho para todas as colunas
                $sheet->getStyle('A5:K5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '444444'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Título (A1)
                $sheet->mergeCells('A1:K1');
                $sheet->setCellValue('A1', 'Relatório de Tecnologias Assistivas');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Filtros (A2)
                $sheet->mergeCells('A2:K2');
                $sheet->setCellValue('A2', 'Filtros: ' . $this->filterText);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Resumo de Totais (A3)
                $sheet->mergeCells('A3:K3');
                $sheet->setCellValue('A3', "Total de Tecnologias: {$totalItems} | Gerado em: " . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Centralizar dados
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 6) {
                    $sheet->getStyle('A6:A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E6:J'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}
