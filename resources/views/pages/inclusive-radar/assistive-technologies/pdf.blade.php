<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - {{ $assistiveTechnology->name }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
        body { font-family: sans-serif; }

        /* Estilos para o layout inteligente de imagens */
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
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 200px;
            font-size: 10px;
            color: #999;
            background: #f9f9f9;
            border: 1px solid #eee;
            margin-bottom: 10px;
            text-align: center;
            padding-top: 80px;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Ficha de Tecnologia Assistiva</h2>
    <p><strong>Nome:</strong> {{ $assistiveTechnology->name }}</p>
    <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    <p><strong>Status:</strong> {{ $assistiveTechnology->is_active ? 'Ativo' : 'Inativo' }}</p>
</div>

{{-- Seção 1: Identificação --}}
<x-pdf.section-title title="1. Identificação do Recurso" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Nome da Tecnologia" :value="$assistiveTechnology->name" colspan="2" />
        <x-pdf.info-item label="Categoria / Tipo" :value="$assistiveTechnology->type->name ?? '---'" colspan="2" />
    </x-pdf.row>
    <x-pdf.row>
        <x-pdf.info-item label="Patrimônio / Tombamento" :value="$assistiveTechnology->asset_code ?? '---'" colspan="2" />
        <x-pdf.info-item label="Quantidade" :value="$assistiveTechnology->quantity" colspan="2" />
    </x-pdf.row>
</x-pdf.table>
<x-pdf.text-area label="Descrição" :value="$assistiveTechnology->description" />

{{-- Seção 2: Especificações Técnicas --}}
@if(count($attributeValues) > 0)
    <x-pdf.section-title title="2. Especificações Técnicas" />
    <x-pdf.table>
        @php $chunks = collect($attributeValues)->chunk(3); @endphp
        @foreach($chunks as $chunk)
            @php
                $count = $chunk->count();
                $colspan = match($count) { 1 => 3, 2 => 1.5, default => 1 };
            @endphp
            <x-pdf.row>
                @foreach($chunk as $attributeId => $value)
                    @php
                        $attributeLabel = $assistiveTechnology->attributeValues
                            ->firstWhere('attribute_id', $attributeId)?->attribute->label ?? '---';
                    @endphp
                    <x-pdf.info-item :label="$attributeLabel" :value="$value" :colspan="$colspan" />
                @endforeach
            </x-pdf.row>
        @endforeach
    </x-pdf.table>
@endif

{{-- Seção 3: Público-Alvo e Status --}}
<x-pdf.section-title title="3. Gestão e Público" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Público-Alvo" :value="$assistiveTechnology->deficiencies->pluck('name')->join(', ') ?: '---'" colspan="2" />
        <x-pdf.info-item label="Requer Treinamento" :value="$assistiveTechnology->requires_training ? 'Sim' : 'Não'" />
        <x-pdf.info-item label="Status do Recurso" :value="$assistiveTechnology->resourceStatus->name ?? '---'" />
    </x-pdf.row>
</x-pdf.table>

{{-- Seção 4: Última Vistoria Estilo MPA --}}
<x-pdf.section-title title="4. Última Vistoria" />

@php
    $lastInspection = $assistiveTechnology->inspections->sortByDesc('inspection_date')->first();
@endphp

@if($lastInspection)
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item
                label="Data e Descrição"
                :value="($lastInspection->inspection_date ? $lastInspection->inspection_date->format('d/m/Y') : '---') . ' - ' . ($lastInspection->description ?: 'Sem descrição')"
                colspan="1"
            />
            <x-pdf.info-item
                label="Estado de Conservação"
                :value="$lastInspection->state?->label() ?? '---'"
                colspan="1"
            />
        </x-pdf.row>
    </x-pdf.table>

    <div class="inspection-images">
        <span class="label" style="display: block; margin-bottom: 8px; font-weight: bold; font-size: 10px;">Imagens da Vistoria</span>

        @if($lastInspection->images->count() > 0)
            @foreach($lastInspection->images as $image)
                @php
                    $base64 = '';
                    $dimensions = null;
                    if (Storage::disk('public')->exists($image->path)) {
                        $imagePath = Storage::disk('public')->path($image->path);
                        $imageData = Storage::disk('public')->get($image->path);
                        $src = @imagecreatefromstring($imageData);

                        if ($src !== false) {
                            $origWidth = imagesx($src);
                            $origHeight = imagesy($src);
                            $dimensions = [$origWidth, $origHeight];

                            // Redimensionamento para otimizar o tamanho do PDF
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
                    <div class="image-placeholder">Arquivo não encontrado ou formato inválido</div>
                @endif
            @endforeach
        @else
            <div style="padding-left: 10px;">
                <span style="font-size: 10px; color: #666;">Nenhuma imagem registrada.</span>
            </div>
        @endif
    </div>
@else
    <x-pdf.text-area label="Última Vistoria" :value="'Nenhuma vistoria registrada.'" />
@endif

<x-pdf.pages />
</body>
</html>
