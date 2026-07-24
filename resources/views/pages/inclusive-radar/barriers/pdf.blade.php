<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório de Barreira - #{{ $barrier->id }}</title>
    <x-pdf.styles />
</head>
<body>
<x-pdf.pages />

<x-pdf.header
    title="Ficha de Identificação de Barreira"
    :status="$barrier->is_active ? 'Ativa' : 'Inativa'"
    :meta="[
        'Barreira' => $barrier->name,
        'Gerado em' => now()->format('d/m/Y H:i'),
    ]"
/>

{{-- Seção 1: Localização --}}
<x-pdf.section-title title="Localização e Contexto" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Campus / Unidade" :value="$barrier->institution->name ?? '---'" colspan="2" />
        <x-pdf.info-item label="Local / Ref." :value="$barrier->location->name ?? '---'" colspan="2" />
    </x-pdf.row>
    <x-pdf.row>
        <x-pdf.info-item label="Coordenadas" :value="($barrier->latitude ?? '—') . ', ' . ($barrier->longitude ?? '—')" colspan="4" />
    </x-pdf.row>
</x-pdf.table>

{{-- Seção 2: Ocorrência e Identificação --}}
<x-pdf.section-title title="Detalhes da Ocorrência" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Nome da Barreira" :value="$barrier->name" colspan="2" />
        <x-pdf.info-item label="Prioridade" :value="$barrier->priority?->label() ?? '---'" />
        <x-pdf.info-item label="Categoria" :value="$barrier->category->name ?? '---'" />
    </x-pdf.row>
    <x-pdf.row>
        <x-pdf.info-item label="Data Identificação" :value="$barrier->identified_at?->format('d/m/Y') ?? '---'" colspan="2" />

        {{-- Lógica de Hierarquia e Combinação do Relator --}}
        @php
            $relatorIdentificado = '---';

            if($barrier->is_anonymous) {
                $relatorIdentificado = 'Relato Anônimo';
            } else {
                $partes = [];

                // Se tiver estudante
                if($barrier->affectedStudent) {
                    $partes[] = 'Estudante: ' . $barrier->affectedStudent->person->name;
                }

                // Se tiver profissional
                if($barrier->affectedProfessional) {
                    $partes[] = 'Profissional: ' . $barrier->affectedProfessional->person->name;
                }

                // Se não tiver nenhum dos dois acima, mas tiver nome manual
                if(empty($partes) && $barrier->affected_person_name) {
                    $role = $barrier->affected_person_role ? ' ('.$barrier->affected_person_role.')' : '';
                    $partes[] = $barrier->affected_person_name . $role;
                }

                // Une as partes com " / " se houver mais de uma
                $relatorIdentificado = !empty($partes) ? implode(' / ', $partes) : 'Relato Geral';
            }
        @endphp
        <x-pdf.info-item label="Relator / Identificação" :value="$relatorIdentificado" colspan="2" />
    </x-pdf.row>
</x-pdf.table>
<x-pdf.text-area label="Descrição do Problema" :value="$barrier->description ?? 'Sem descrição.'" />

{{-- Seção 3: Impacto --}}
<x-pdf.section-title title="Público-Alvo Afetado" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Deficiências Relacionadas" :value="$barrier->deficiencies->pluck('name')->join(', ') ?: 'Geral / Não especificado'" colspan="4" />
    </x-pdf.row>
</x-pdf.table>

{{-- Seção 4: Última Vistoria --}}
<x-pdf.section-title title="Última Vistoria" />

@php
    $lastInspection = $barrier->inspections
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
                <strong>Estado da Barreira:</strong> {{ $lastInspection->status?->label() ?? '---' }}
            </td>
        </tr>
        <tr>
            <td class="pdf-cell" colspan="2">
                <strong>Parecer Técnico</strong>
                <div class="long-text">
                    {!! \App\Support\RichTextSanitizer::sanitize((string) ($lastInspection->description ?: 'Sem descrição registrada.')) !!}
                </div>
            </td>
        </tr>
    </table>

    {{-- Container de Imagens FORA da tabela para permitir quebra de página --}}
    <x-pdf.section-title title="Evidências Visuais" />
    <div class="evidence-container">
        @if($lastInspection->images->count() > 0)
            @foreach($lastInspection->images as $image)
                @php
                    $path = public_path('storage/' . $image->path);
                @endphp

                @if(file_exists($path))
                    <div class="evidence-card">
                        <img class="evidence-image" src="{{ $path }}" alt="Evidência visual da vistoria">
                        <div class="evidence-caption">
                            Evidência {{ $loop->iteration }} de {{ $lastInspection->images->count() }}
                        </div>
                    </div>
                @else
                    <div class="evidence-placeholder">
                        Evidência {{ $loop->iteration }}: imagem não encontrada.
                    </div>
                @endif
            @endforeach
        @else
            <span class="value">Nenhuma imagem registrada.</span>
        @endif
    </div>
@else
    <x-pdf.text-area label="Última Vistoria" :value="'Nenhuma vistoria técnica registrada até o momento.'" />
@endif

<div class="pdf-mt-60">
    <x-pdf.table-signatures>
        @if(!$barrier->is_anonymous)
            {{-- 1. Se houver Estudante selecionado --}}
            @if($barrier->affectedStudent)
                <x-pdf.table-signature-label label="Assinatura do Estudante Contribuidor" />
            @endif

            {{-- 2. Se houver Profissional selecionado --}}
            @if($barrier->affectedProfessional)
                <x-pdf.table-signature-label label="Assinatura do Profissional Contribuidor" />
            @endif

            {{-- 3. Se não for nenhum dos dois acima, mas for relato manual --}}
            @if(!$barrier->affectedStudent && !$barrier->affectedProfessional && $barrier->affected_person_name)
                <x-pdf.table-signature-label label="Assinatura do Contribuidor" />
            @endif
        @endif

        {{-- 4. Assinatura Técnica (Sempre presente) --}}
        <x-pdf.table-signature-label label="RESPONSÁVEL PELO SETOR / CARIMBO" />
    </x-pdf.table-signatures>
</div>

</body>
</html>
