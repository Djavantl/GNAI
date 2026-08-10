<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - {{ $assistiveTechnology->name }}</title>

    <x-pdf.styles />
</head>

<body>

<x-pdf.header
    title="Ficha de Tecnologia Assistiva"
    :status="$assistiveTechnology->is_active ? 'Ativo' : 'Inativo'"
    :meta="[
        'Nome' => $assistiveTechnology->name,
        'Patrimônio' => $assistiveTechnology->asset_code ?? '---',
        'Gerado em' => now()->format('d/m/Y H:i'),
    ]"
/>

<div class="category-header">Informações da Tecnologia Assistiva</div>

{{-- ================= DADOS BÁSICOS ================= --}}
<div class="section-title">Dados Básicos</div>

<table class="pdf-table">
    <tr>
        <td class="pdf-cell pdf-w-50">
            <strong>Nome:</strong> {!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($assistiveTechnology->name ?? '---')) !!}
        </td>
        <td class="pdf-cell pdf-w-50">
            <strong>Natureza:</strong> {{ $assistiveTechnology->is_digital ? 'Recurso Digital' : 'Recurso Físico' }}
        </td>
    </tr>

    <tr>
        <td class="pdf-cell">
            <strong>Patrimônio / Tombamento:</strong> {!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($assistiveTechnology->asset_code ?? '---')) !!}
        </td>
        <td class="pdf-cell">
            <strong>Status no Sistema:</strong> {{ $assistiveTechnology->is_active ? 'Ativo' : 'Inativo' }}
        </td>
    </tr>

    <tr>
        <td class="pdf-cell">
            <strong>Quantidade Total:</strong> {{ $assistiveTechnology->quantity ?? '---' }}
        </td>
        <td class="pdf-cell">
            <strong>Quantidade Disponível:</strong> {{ $assistiveTechnology->quantity_available ?? '---' }}
        </td>
    </tr>
</table>

<div class="section-title">Descrição</div>
<table class="pdf-table">
    <tr>
        <td class="pdf-cell" colspan="4">
            <strong>Descrição Detalhada</strong>
            <div class="long-text">
                {!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($assistiveTechnology->notes ?: '---')) !!}
            </div>
        </td>
    </tr>
</table>

<div class="category-header">Gestão e Público</div>

{{-- ================= CONTROLE DO RECURSO ================= --}}
<div class="section-title">Controle do Recurso</div>

<table class="pdf-table">
    <tr>
        <td class="pdf-cell" colspan="2">
            <strong>Status do Recurso:</strong> {{ $assistiveTechnology->status?->label() ?? '---' }}
        </td>
        <td class="pdf-cell" colspan="2">
            <strong>Permite Empréstimos:</strong> {{ $assistiveTechnology->is_loanable ? 'Sim' : 'Não' }}
        </td>
    </tr>
</table>

{{-- ================= PÚBLICO-ALVO ================= --}}
<div class="section-title">Público-Alvo</div>

<table class="pdf-table">
    <tr>
        <td class="pdf-cell" colspan="4">
            <strong>Deficiências Atendidas:</strong>
            {!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($assistiveTechnology->deficiencies->pluck('name')->join(', ') ?: '---')) !!}
        </td>
    </tr>
</table>

<div class="category-header">Vistorias</div>

{{-- ================= ÚLTIMA VISTORIA ================= --}}
<div class="section-title">Última Vistoria</div>

@php
    $lastInspection = $assistiveTechnology->inspections
        ->sortByDesc('inspection_date')
        ->first();
@endphp

@if($lastInspection)

    <table class="pdf-table">
        <tr>
            <td class="pdf-cell">
                <strong>Data:</strong> {{ $lastInspection->inspection_date?->format('d/m/Y') ?? '---' }}
            </td>
            <td class="pdf-cell">
                <strong>Tipo de Vistoria:</strong> {{ $lastInspection->type?->label() ?? '---' }}
            </td>
            <td class="pdf-cell">
                <strong>Estado de Conservação:</strong> {{ $lastInspection->state?->label() ?? '---' }}
            </td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="3">
                <strong>Parecer Técnico</strong>
                <div class="long-text">
                    {!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($lastInspection->description ?: 'Sem descrição registrada.')) !!}
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Evidências</div>
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
                            imagecopyresampled($resized, $src, 0, 0, 0, 0,
                                $newWidth, $newHeight, $width, $height);

                            ob_start();
                            imagejpeg($resized, null, 80);
                            $base64 = 'data:image/jpeg;base64,' . base64_encode(ob_get_clean());

                            imagedestroy($src);
                            imagedestroy($resized);
                        }
                    }

                    if (!$base64 || !$ratio) {
                        $evidenceClass = null;
                    } elseif ($ratio > 1.5) {
                        $evidenceClass = 'wide';
                    } elseif ($ratio < 0.67) {
                        $evidenceClass = 'tall';
                    } else {
                        $evidenceClass = 'square';
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
            <div class="evidence-placeholder">
                Nenhuma evidência registrada.
            </div>
        @endif
    </div>

@else

    <table class="pdf-table">
        <tr>
            <td class="pdf-cell">Nenhuma vistoria registrada.</td>
        </tr>
    </table>

@endif

</body>
</html>
