<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - {{ $material->name }}</title>
    <x-pdf.styles />
</head>
<body>
<x-pdf.pages />

<x-pdf.header
    title="Ficha de Material Pedagógico Acessível"
    :status="$material->is_active ? 'Ativo' : 'Inativo'"
    :meta="[
        'Nome' => $material->name,
        'Gerado em' => now()->format('d/m/Y H:i'),
    ]"
/>

<x-pdf.section-title title="Identificação do Recurso" />

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

<x-pdf.section-title title="Gestão e Público" />

<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Status do Recurso" :value="$material->status?->label() ?? '---'" />
        <x-pdf.info-item label="Permite Empréstimos" :value="$material->is_loanable ? 'Sim' : 'Não'" />
        <x-pdf.info-item label="Quantidade Disponível" :value="$material->quantity_available ?? '---'" colspan="2" />
    </x-pdf.row>

    <x-pdf.row>
        <x-pdf.info-item label="Público-Alvo (Deficiências Atendidas)" :value="$material->deficiencies->pluck('name')->join(', ') ?: '---'" colspan="2" />
        <x-pdf.info-item label="Recursos de Acessibilidade" :value="$material->accessibilityFeatures->pluck('name')->join(', ') ?: '---'" colspan="2" />
    </x-pdf.row>
</x-pdf.table>

<x-pdf.section-title title="Última Vistoria" />
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

    <x-pdf.section-title title="Evidências Visuais" />
    <div class="inspection-images">
        @if($lastInspection->images->count() > 0)
            @foreach($lastInspection->images as $image)
                @php
                    $base64 = null;
                    $ratio = null;

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

                    if ($base64 && $ratio) {
                        if ($ratio > 1.5) $imageClass = 'wide';
                        elseif ($ratio < 0.67) $imageClass = 'tall';
                        else $imageClass = 'square';
                    } else {
                        $imageClass = null;
                    }
                @endphp

                @if($base64)
                    <div class="evidence-card {{ $imageClass }}">
                        <img class="evidence-image" src="{{ $base64 }}" alt="Evidência visual da vistoria">
                        <div class="evidence-caption">
                            Evidência {{ $loop->iteration }} de {{ $lastInspection->images->count() }}
                        </div>
                    </div>
                @else
                    <div class="image-placeholder">
                        Evidência {{ $loop->iteration }}: arquivo não encontrado ou formato inválido.
                    </div>
                @endif
            @endforeach
        @else
            <div class="image-placeholder">Nenhuma imagem registrada.</div>
        @endif
    </div>
@else
    <x-pdf.text-area label="Última Vistoria" :value="'Nenhuma vistoria registrada.'" />
@endif

</body>
</html>
