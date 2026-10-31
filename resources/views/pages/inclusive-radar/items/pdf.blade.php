<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - {{ $item->name }}</title>
    <x-pdf.styles />
</head>
<body>

<x-pdf.header
    title="Ficha de Item"
    :status="$item->is_active ? 'Ativo' : 'Inativo'"
    :meta="[
        'Tipo' => $item->item_type?->label() ?? '---',
        'Descrição' => $item->name,
        'Gerado em' => now()->format('d/m/Y H:i'),
    ]"
/>

<x-pdf.section-title title="Identificação do Recurso" />

<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Tipo do Item" :value="$item->item_type?->label() ?? '---'" />
        <x-pdf.info-item label="Descrição" :value="$item->name" />
        <x-pdf.info-item label="Natureza" :value="$item->is_digital ? 'Recurso Digital' : 'Recurso Físico'" colspan="2" />
    </x-pdf.row>
    <x-pdf.row>
        <x-pdf.info-item label="Patrimônio / Tombamento" :value="$item->asset_code ?? '---'" colspan="2" />
        <x-pdf.info-item label="Quantidade Total" :value="$item->quantity" colspan="2" />
    </x-pdf.row>
</x-pdf.table>
<x-pdf.text-area label="Descrição Detalhada" :value="$item->notes ?: '---'" />

<x-pdf.section-title title="Gestão e Público" />

<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Status do Recurso" :value="$item->status?->label() ?? '---'" />
        <x-pdf.info-item label="Permite Empréstimos" :value="$item->is_loanable ? 'Sim' : 'Não'" />
        <x-pdf.info-item label="Quantidade Disponível" :value="$item->quantity_available ?? '---'" colspan="2" />
    </x-pdf.row>

    <x-pdf.row>
        <x-pdf.info-item label="Público-Alvo (Deficiências Atendidas)" :value="$item->deficiencies->pluck('name')->join(', ') ?: '---'" colspan="2" />
        <x-pdf.info-item label="Recursos de Acessibilidade" :value="$item->accessibilityFeatures->pluck('name')->join(', ') ?: '---'" colspan="2" />
    </x-pdf.row>
</x-pdf.table>

<x-pdf.section-title title="Última Vistoria" />
@php
    $lastInspection = $item->inspections->sortByDesc('inspection_date')->first();
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

    <x-pdf.section-title title="Evidências" />
    <div class="inspection-evidences">
        @if($lastInspection->evidences->count() > 0)
            @foreach($lastInspection->evidences as $evidence)
                @php
                    $base64 = null;
                    $ratio = null;

                    if ($evidence->isImage() && Storage::disk('public')->exists($evidence->path)) {
                        $imageData = Storage::disk('public')->get($evidence->path);
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
                        if ($ratio > 1.5) $evidenceClass = 'wide';
                        elseif ($ratio < 0.67) $evidenceClass = 'tall';
                        else $evidenceClass = 'square';
                    } else {
                        $evidenceClass = null;
                    }
                @endphp

                @if($base64)
                    <div class="evidence-card {{ $evidenceClass }}">
                        <img class="evidence-image" src="{{ $base64 }}" alt="Evidência da vistoria">
                        <div class="evidence-caption">
                            Evidência {{ $loop->iteration }} de {{ $lastInspection->evidences->count() }}
                        </div>
                    </div>
                @else
                    <div class="evidence-placeholder">
                        Evidência {{ $loop->iteration }}: {{ $evidence->displayName() }}
                    </div>
                @endif
            @endforeach
        @else
            <div class="evidence-placeholder">Nenhuma evidência registrada.</div>
        @endif
    </div>
@else
    <x-pdf.text-area label="Última Vistoria" :value="'Nenhuma vistoria registrada.'" />
@endif

</body>
</html>
