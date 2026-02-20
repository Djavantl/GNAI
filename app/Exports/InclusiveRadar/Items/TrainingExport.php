<?php

namespace App\Exports\InclusiveRadar\Items;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{FromCollection,
    ShouldAutoSize,
    WithCustomStartCell,
    WithEvents,
    WithHeadings,
    WithMapping,
    WithTitle};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TrainingExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithEvents,
    WithCustomStartCell,
    WithTitle
{
    protected Collection $items;
    protected string $trainingTitle;
    protected string $trainingStatus;

    public function __construct($items, string $trainingTitle = '', string $trainingStatus = 'Ativo')
    {
        $this->items = collect($items);
        $this->trainingTitle = $trainingTitle;
        $this->trainingStatus = $trainingStatus;
    }

    public function title(): string
    {
        return 'Treinamento';
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
            'Tipo de Recurso',
            'Recurso Vinculado',
            'Ativo',
            'Links de Tutoriais',
            'Qtd. Arquivos Anexados',
            'Observação'
        ];
    }

    public function map($item): array
    {
        $trainableTypeLabel = match($item->trainable_type) {
            'assistive_technology' => 'Tecnologia Assistiva',
            'accessible_educational_material' => 'Material Pedagógico Acessível',
            default => '---',
        };

        $trainableName = $item->trainable?->name ?? '---';
        $tutorialLinks = is_array($item->url) ? implode(' | ', $item->url) : '';

        return [
            $item->id,
            $item->description ?? '---',
            $trainableTypeLabel,
            $trainableName,
            $item->is_active ? 'Sim' : 'Não',
            $tutorialLinks,
            $item->files->count(),
            $item->files->count() > 0 ? 'Para acessar os arquivos anexados, utilize o sistema.' : '---'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Título da ficha
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'FICHA DE TREINAMENTO');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Função para criar linha label + valor centralizada
                $setLabelValue = function($cell, $label, $value) use ($sheet) {
                    $sheet->mergeCells($cell . ':H' . substr($cell, 1)); // Merge até H
                    $richText = new RichText();
                    $bold = $richText->createTextRun($label . ' ');
                    $bold->getFont()->setBold(true);
                    $richText->createText($value);
                    $sheet->setCellValue($cell, $richText);
                    $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                };

                // Informações do treinamento
                $setLabelValue('A2', 'Título:', $this->trainingTitle);
                $setLabelValue('A3', 'Gerado em:', now()->format('d/m/Y H:i'));
                $setLabelValue('A4', 'Status:', $this->trainingStatus);

                // Cabeçalho da tabela
                $sheet->getStyle('A6:H6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '444444']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Centralizar colunas da tabela
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A7:A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // ID
                $sheet->getStyle('E7:H'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Ativo, Qtd Arquivos, Observação
                $sheet->getStyle('B7:D'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Descrição, Tipo, Recurso
            },
        ];
    }
}
