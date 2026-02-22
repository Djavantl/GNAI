<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório de Manutenção - #{{ $maintenance->id }}</title>
    <style>
        {!! file_exists(resource_path('css/components/pdf.css')) ? file_get_contents(resource_path('css/components/pdf.css')) : '' !!}
        body { font-family: sans-serif; }

        /* Estilos padronizados para o layout inteligente de imagens */
        .inspection-images {
            width: 100%;
            border: 1px solid #ccc;
            border-top: none;
            padding: 10px;
            background: #fff;
        }
        .image-container {
            margin-bottom: 10px;
            page-break-inside: avoid;
            background-color: #f9f9f9;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            border: 1px solid #eee;
        }
        .image-container.wide {
            width: 100%;
            height: 300px;
        }
        .image-container.tall {
            width: 100%;
            height: 700px;
            page-break-before: always;
            page-break-after: always;
        }
        .image-container.square {
            width: 100%;
            height: 400px;
        }
        .image-placeholder {
            width: 100%;
            height: 200px;
            font-size: 10px;
            color: #999;
            background: #f9f9f9;
            border: 1px solid #eee;
            margin-bottom: 10px;
            text-align: center;
            line-height: 200px;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Dossiê Técnico de Manutenção</h2>
    <p><strong>Equipamento:</strong> {{ $maintenance->maintainable->name }}</p>
    <p><strong>Chamado:</strong> #{{ $maintenance->id }}</p>
    <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
</div>

{{-- 1. Identificação --}}
<x-pdf.section-title title="1. Identificação do Recurso" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Nome do Recurso" :value="$maintenance->maintainable->name" colspan="2" />
        <x-pdf.info-item label="Status da Manutenção" :value="$maintenance->status->label()" colspan="2" />
    </x-pdf.row>
</x-pdf.table>

{{-- 2. Diagnóstico --}}
@php $stage1 = $maintenance->stages->firstWhere('step_number', 1); @endphp
<x-pdf.section-title title="2. Diagnóstico e Abertura" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Responsável" :value="$stage1->starter->name ?? '---'" colspan="2" />
        <x-pdf.info-item label="Custo Estimado" :value="'R$ ' . number_format($stage1->estimated_cost ?? 0, 2, ',', '.')" colspan="2" />
    </x-pdf.row>
</x-pdf.table>
<x-pdf.text-area label="Descrição do Problema" :value="$stage1->damage_description" />

{{-- 3. Finalização --}}
@php $stage2 = $maintenance->stages->firstWhere('step_number', 2); @endphp
@if($stage2 && $stage2->completed_at)
    <x-pdf.section-title title="3. Finalização Técnica" />
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Técnico" :value="$stage2->user->name ?? '---'" colspan="2" />
            <x-pdf.info-item label="Custo Real" :value="'R$ ' . number_format($stage2->real_cost ?? 0, 2, ',', '.')" colspan="2" />
        </x-pdf.row>
    </x-pdf.table>
    <x-pdf.text-area label="Parecer Técnico Final" :value="$stage2->observation" />

    {{-- 4. Registro Fotográfico Padronizado --}}
    <x-pdf.section-title title="4. Vistoria de Saída e Fotos" />

    @php
        $inspection = $stage2->inspection ?? $maintenance->maintainable->inspections
            ->where('type', \App\Enums\InclusiveRadar\InspectionType::MAINTENANCE->value)
            ->whereBetween('created_at', [$stage2->completed_at->startOfDay(), $stage2->completed_at->endOfDay()])
            ->first();
    @endphp

    @if($inspection)
        <x-pdf.table>
            <x-pdf.row>
                <x-pdf.info-item label="Data da Vistoria" :value="$inspection->created_at->format('d/m/Y')" colspan="2" />
                <x-pdf.info-item label="Estado de Conservação" :value="$inspection->state?->label() ?? '---'" colspan="2" />
            </x-pdf.row>
        </x-pdf.table>

        <div class="inspection-images">
            <span class="label" style="display: block; margin-bottom: 8px; font-weight: bold; font-size: 10px;">Evidências Fotográficas</span>

            @if($inspection->images->count() > 0)
                @foreach($inspection->images as $image)
                    @php
                        $base64 = '';
                        $dimensions = null;
                        if (Storage::disk('public')->exists($image->path)) {
                            $imageData = Storage::disk('public')->get($image->path);
                            $src = @imagecreatefromstring($imageData);

                            if ($src !== false) {
                                $origWidth = imagesx($src);
                                $origHeight = imagesy($src);
                                $dimensions = [$origWidth, $origHeight];

                                // Redimensionamento inteligente
                                $maxSize = 1000;
                                if ($origWidth > $origHeight) {
                                    $newWidth = $maxSize;
                                    $newHeight = (int) round($origHeight * $maxSize / $origWidth);
                                } else {
                                    $newHeight = $maxSize;
                                    $newWidth = (int) round($origWidth * $maxSize / $origHeight);
                                }

                                $resized = imagecreatetruecolor($newWidth, $newHeight);
                                imagecopyresampled($resized, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

                                ob_start();
                                imagejpeg($resized, null, 80);
                                $base64 = 'data:image/jpeg;base64,' . base64_encode(ob_get_clean());

                                imagedestroy($src);
                                imagedestroy($resized);
                            }
                        }
                    @endphp

                    @if($base64 && $dimensions)
                        @php
                            $ratio = $dimensions[0] / $dimensions[1];
                            if ($ratio > 1.5) { $imageClass = 'wide'; }
                            elseif ($ratio < 0.67) { $imageClass = 'tall'; }
                            else { $imageClass = 'square'; }
                        @endphp
                        <div class="image-container {{ $imageClass }}" style="background-image: url('{{ $base64 }}');"></div>
                    @else
                        <div class="image-placeholder">Imagem não disponível</div>
                    @endif
                @endforeach
            @else
                <p style="font-size: 10px; color: #666; padding-left: 10px;">Nenhuma imagem anexada a esta manutenção.</p>
            @endif
        </div>
    @else
        <x-pdf.text-area label="Vistoria" :value="'Nenhum registro de vistoria vinculado à finalização.'" />
    @endif
@endif

<div style="margin-top: 50px; text-align: center; font-size: 10px; page-break-inside: avoid;">
    <p>_________________________________________________________</p>
    <p>Assinatura do Técnico: {{ $stage2->user->name ?? '________________' }}</p>
</div>

<x-pdf.pages />
</body>
</html>
