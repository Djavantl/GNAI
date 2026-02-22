<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - {{ $material->name }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
        body { font-family: sans-serif; }
        /* Estilos adicionais para o layout de imagens */
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
        }
        .image-container.wide {
            width: 100%;
            height: 300px;
            background-color: #f9f9f9;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            border: 1px solid #eee;
        }
        .image-container.tall {
            width: 100%;
            height: 700px;
            background-color: #f9f9f9;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            border: 1px solid #eee;
            page-break-before: always;
            page-break-after: always;
        }
        .image-container.square {
            width: 100%;
            height: 400px;
            background-color: #f9f9f9;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            border: 1px solid #eee;
        }
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
    <p><strong>Status:</strong> {{ $material->is_active ? 'Ativo' : 'Inativo' }}</p>
</div>

{{-- 1. Identificação --}}
<x-pdf.section-title title="1. Identificação do Recurso" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Título do Material" :value="$material->name" colspan="2" />
        <x-pdf.info-item label="Categoria / Tipo" :value="$material->type->name ?? '---'" colspan="2" />
    </x-pdf.row>
    <x-pdf.row>
        <x-pdf.info-item label="Patrimônio / Tombamento" :value="$material->asset_code ?? '---'" colspan="2" />
        <x-pdf.info-item label="Quantidade" :value="$material->quantity" colspan="2" />
    </x-pdf.row>
</x-pdf.table>
<x-pdf.text-area label="Descrição" :value="$material->notes" />

{{-- 2. Especificações Técnicas --}}
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
                        $attributeLabel = $material->attributeValues
                            ->firstWhere('attribute_id', $attributeId)?->attribute->label ?? '---';
                    @endphp
                    <x-pdf.info-item :label="$attributeLabel" :value="$value" :colspan="$colspan" />
                @endforeach
            </x-pdf.row>
        @endforeach
    </x-pdf.table>
@endif

{{-- 3. Gestão e Público --}}
<x-pdf.section-title title="3. Gestão e Público" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Público-Alvo" :value="$material->deficiencies->pluck('name')->join(', ') ?: '---'" colspan="2" />
        <x-pdf.info-item label="Recursos de Acessibilidade" :value="$material->accessibilityFeatures->pluck('name')->join(', ') ?: '---'" colspan="2" />
    </x-pdf.row>
    <x-pdf.row>
        <x-pdf.info-item label="Status do Recurso" :value="$material->resourceStatus->name ?? '---'" colspan="2" />
        <x-pdf.info-item label="Tipo de Registro" :value="'Material Pedagógico'" colspan="2" />
    </x-pdf.row>
</x-pdf.table>

{{-- 4. Última Vistoria --}}
<x-pdf.section-title title="4. Última Vistoria" />

@php
    $lastInspection = $material->inspections->sortByDesc('inspection_date')->first();
@endphp

@if($lastInspection)
    {{-- Tabela para os dados textuais (Data e Estado) --}}
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item
                label="Data e Parecer"
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

    {{-- Container de imagens --}}
    <div class="inspection-images">
        <span class="label" style="display: block; margin-bottom: 8px; font-weight: bold; font-size: 10px;">Imagens da Vistoria</span>

        @if($lastInspection->images->count() > 0)
            @foreach($lastInspection->images as $image)
                @php
                    $base64 = '';
                    $dimensions = null;
                    $imagePath = Storage::disk('public')->path($image->path);
                    if (Storage::disk('public')->exists($image->path)) {
                        // Carrega a imagem original
                        $imageData = Storage::disk('public')->get($image->path);
                        // Cria uma imagem GD a partir dos dados
                        $src = @imagecreatefromstring($imageData);
                        if ($src !== false) {
                            // Obtém dimensões originais
                            $origWidth = imagesx($src);
                            $origHeight = imagesy($src);
                            $dimensions = [$origWidth, $origHeight];

                            // Define tamanho máximo para redimensionamento
                            $maxSize = 1200; // pixels

                            // Calcula novas dimensões mantendo proporção
                            if ($origWidth > $origHeight) {
                                // Imagem larga
                                $newWidth = $maxSize;
                                $newHeight = (int) round($origHeight * $maxSize / $origWidth);
                            } else {
                                // Imagem alta ou quadrada
                                $newHeight = $maxSize;
                                $newWidth = (int) round($origWidth * $maxSize / $origHeight);
                            }

                            // Cria uma nova imagem redimensionada
                            $resized = imagecreatetruecolor($newWidth, $newHeight);
                            imagecopyresampled($resized, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

                            // Captura a imagem redimensionada em formato JPEG com qualidade 80
                            ob_start();
                            imagejpeg($resized, null, 80);
                            $imageDataResized = ob_get_clean();

                            // Libera memória
                            imagedestroy($src);
                            imagedestroy($resized);

                            // Codifica em base64
                            $base64 = 'data:image/jpeg;base64,' . base64_encode($imageDataResized);
                        } else {
                            // Falha ao criar imagem, tenta usar a original (pode ser muito grande)
                            // Como fallback, converte a original para base64 (pode não funcionar)
                            $extension = pathinfo($image->path, PATHINFO_EXTENSION);
                            $base64 = 'data:image/' . $extension . ';base64,' . base64_encode($imageData);
                            // Tenta obter dimensões com getimagesize (pode falhar em arquivos muito grandes)
                            $dimensions = @getimagesize($imagePath);
                        }
                    }
                @endphp

                @if($base64 && $dimensions)
                    @php
                        $width = $dimensions[0];
                        $height = $dimensions[1];
                        $ratio = $width / $height;

                        // Define classes com base na proporção original
                        if ($ratio > 1.5) {
                            $imageClass = 'wide';
                        } elseif ($ratio < 0.67) {
                            $imageClass = 'tall';
                        } else {
                            $imageClass = 'square';
                        }
                    @endphp

                    <div class="image-container {{ $imageClass }}" style="background-image: url('{{ $base64 }}');">
                        {{-- Conteúdo vazio --}}
                    </div>
                @else
                    <div class="image-placeholder" style="height: 200px; margin-bottom: 10px;">
                        Arquivo não encontrado ou formato inválido
                    </div>
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
