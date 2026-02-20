<?php

namespace App\Exports\InclusiveRadar\Items;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithEvents,
    WithCustomStartCell,
    WithTitle
};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssistiveTechnologyExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithEvents,
    WithCustomStartCell,
    WithTitle
{
    protected Collection $items;
    protected string $resourceName;
    protected string $resourceStatus;

    public function __construct($items, string $resourceName = '', string $resourceStatus = 'Ativo')
    {
        $this->items = collect($items);
        $this->resourceName = $resourceName;
        $this->resourceStatus = $resourceStatus;
    }

    public function title(): string
    {
        return 'Tecnologia Assistiva';
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Descrição',
            'Tipo / Categoria',
            'Cód. Patrimônio',
            'Qtd. Total',
            'Qtd. Disponível',
            'Estado de Conservação',
            'Requer Treinamento',
            'Deficiências',
            'Última Vistoria - Data',
            'Última Vistoria - Tipo',
            'Última Vistoria - Estado',
            'Última Vistoria - Observações',
        ];
    }

    public function map($item): array
    {
        // Descrição do recurso
        $description = wordwrap($item->description ?? '---', 50, "\n", true);

        // Última vistoria
        $lastInspection = $item->inspections->first();
        if ($lastInspection) {
            $inspectionDate = $lastInspection->inspection_date?->format('d/m/Y') ?? '---';

            // Usa label do relacionamento / enum para tipo e estado
            $inspectionType = $lastInspection->type?->label() ?? '---';
            $inspectionState = $lastInspection->state?->label() ?? '---';
            $inspectionDescription = wordwrap($lastInspection->description ?: 'Nada declarado.', 50, "\n", true);
        } else {
            $inspectionDate = '---';
            $inspectionType = '---';
            $inspectionState = '---';
            $inspectionDescription = 'Nenhuma vistoria registrada.';
        }

        return [
            $item->id,
            $description,
            $item->type?->name ?? '---',
            $item->asset_code ?? '---',
            $item->quantity ?? 0,
            $item->quantity_available ?? 0,
            $item->conservation_state?->label() ?? '---',
            $item->requires_training ? 'Sim' : 'Não',
            $item->deficiencies->pluck('name')->implode(', ') ?: '---',
            $inspectionDate,
            $inspectionType,
            $inspectionState,
            $inspectionDescription,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // ----------------- TÍTULO -----------------
                $sheet->mergeCells('A1:M1');
                $sheet->setCellValue('A1', 'FICHA DE TECNOLOGIA ASSISTIVA');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // ----------------- INFO PRINCIPAL -----------------
                $setLabelValue = function ($cell, $label, $value) use ($sheet) {
                    $sheet->mergeCells($cell . ':M' . substr($cell, 1));
                    $richText = new RichText();
                    $bold = $richText->createTextRun($label . ' ');
                    $bold->getFont()->setBold(true);
                    $richText->createText($value);
                    $sheet->setCellValue($cell, $richText);
                    $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                };

                $setLabelValue('A2', 'Nome:', $this->resourceName);
                $setLabelValue('A3', 'Gerado em:', now()->format('d/m/Y H:i'));
                $setLabelValue('A4', 'Status:', $this->resourceStatus);

                // ----------------- CABEÇALHO -----------------
                $sheet->getStyle('A6:M6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '444444']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // ----------------- ALINHAMENTO -----------------
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 7) {
                    // Campos pequenos -> centralizados
                    $sheet->getStyle('A7:A'.$lastRow)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    $sheet->getStyle('C7:L'.$lastRow)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    // Descrição e Última Vistoria - Observações -> wrapText, esquerda, vertical center
                    $sheet->getStyle('B7:B'.$lastRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);

                    $sheet->getStyle('M7:M'.$lastRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);
                }
            }
        ];
    }
}
