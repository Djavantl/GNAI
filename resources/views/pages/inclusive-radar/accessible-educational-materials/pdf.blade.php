<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - {{ $material->name }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
        body { font-family: sans-serif; }

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
        .image-container.wide { height: 300px; }
        .image-container.tall { height: 700px; page-break-before: always; page-break-after: always; }
        .image-container.square { height: 400px; }
        .image-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            font-size: 10px;
            color: #999;
            background: #f9f9f9;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Ficha de Material Pedagógico Acessível</h2>
    <p><strong>Nome:</strong> {{ $material->name }}</p>
    <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    <p><strong>Status no Sistema:</strong> {{ $material->is_active ? 'Ativo' : 'Inativo' }}</p>
</div>

{{-- 1. IDENTIFICAÇÃO --}}
<x-pdf.section-title title="1. Identificação do Recurso" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Nome" :value="$material->name" colspan="2" />
        <x-pdf.info-item label="Natureza" :value="$material->is_digital ? 'Recurso Digital' : 'Recurso Físico'" colspan="2" />
    </x-pdf.row>
    <x-pdf.row>
        <x-pdf.info-item label="Patrimônio / Tombamento" :value="$material->asset_code ?? '---'" colspan="2" />
        <x-pdf.info-item label="Quantidade Total" :value="$material->quantity" colspan="2" />
    </x-pdf.row>
</x-pdf.table>
<x-pdf.text-area label="Descrição Detalhada" :value="$material->notes ?: '---'" />

{{-- 2. GESTÃO E PÚBLICO --}}
<x-pdf.section-title title="2. Gestão e Público" />
<x-pdf.table>
    {{-- Linha 1 --}}
    <x-pdf.row>
        <x-pdf.info-item label="Status do Recurso" :value="$material->status?->label() ?? '---'" />
        <x-pdf.info-item label="Permite Empréstimos" :value="$material->is_loanable ? 'Sim' : 'Não'" />
        {{-- Adicionado colspan="2" abaixo para fechar a grade de 4 colunas --}}
        <x-pdf.info-item label="Quantidade Disponível" :value="$material->quantity_available ?? '---'" colspan="2" />
    </x-pdf.row>

    {{-- Linha 2 --}}
    <x-pdf.row>
        <x-pdf.info-item label="Público-Alvo (Deficiências Atendidas)" :value="$material->deficiencies->pluck('name')->join(', ') ?: '---'" colspan="2" />
        <x-pdf.info-item label="Recursos de Acessibilidade" :value="$material->accessibilityFeatures->pluck('name')->join(', ') ?: '---'" colspan="2" />
    </x-pdf.row>
</x-pdf.table>

{{-- 3. ÚLTIMA VISTORIA --}}
<x-pdf.section-title title="3. Última Vistoria" />
@php
    $lastInspection = $material->inspections->sortByDesc('inspection_date')->first();
@endphp

@if($lastInspection)
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Data" :value="$lastInspection->inspection_date?->format('d/m/Y') ?? '---'" />
            <x-pdf.info-item label="Tipo de Vistoria" :value="$lastInspection->type?->label() ?? '---'" />
            <x-pdf.info-item label="Estado de Conservação" :value="$lastInspection->state?->label() ?? '---'" />
        </x-pdf.row>
    </x-pdf.table>

    <x-pdf.text-area label="Parecer Técnico" :value="$lastInspection->description ?: 'Sem descrição registrada.'" />

    <div class="inspection-images">
        <strong style="font-size:10px;">Imagens da Vistoria</strong>
        @if($lastInspection->images->count() > 0)
            @foreach($lastInspection->images as $image)
                @php
                    $base64 = null;
                    if (Storage::disk('public')->exists($image->path)) {
                        $imageData = Storage::disk('public')->get($image->path);
                        $src = @imagecreatefromstring($imageData);
                        if ($src !== false) {
                            $width = imagesx($src);
                            $height = imagesy($src);
                            $ratio = $width / $height;

                            $maxSize = 1000;
                            if ($ratio > 1) {
                                $newWidth = $maxSize;
                                $newHeight = intval($height * $maxSize / $width);
                            } else {
                                $newHeight = $maxSize;
                                $newWidth = intval($width * $maxSize / $height);
                            }

                            $resized = imagecreatetruecolor($newWidth, $newHeight);
                            imagecopyresampled($resized, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                            ob_start();
                            imagejpeg($resized, null, 80);
                            $base64 = 'data:image/jpeg;base64,' . base64_encode(ob_get_clean());

                            imagedestroy($src);
                            imagedestroy($resized);
                        }
                    }

                    if ($base64) {
                        if ($ratio > 1.5) $imageClass = 'wide';
                        elseif ($ratio < 0.67) $imageClass = 'tall';
                        else $imageClass = 'square';
                    }
                @endphp

                @if($base64)
                    <div class="image-container {{ $imageClass }}" style="background-image: url('{{ $base64 }}');"></div>
                @else
                    <div class="image-placeholder">Arquivo não encontrado ou formato inválido.</div>
                @endif
            @endforeach
        @else
            <div class="image-placeholder">Nenhuma imagem registrada.</div>
        @endif
    </div>
@else
    <x-pdf.text-area label="Última Vistoria" :value="'Nenhuma vistoria registrada.'" />
@endif

<x-pdf.pages />
</body>
</html>
