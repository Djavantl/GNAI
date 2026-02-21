<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório de Barreira - #{{ $barrier->id }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
        .evidence-container { border: 1px solid #ccc; border-top: none; padding: 10px; background: #fff; }
        .evidence-grid { width: 100%; }
        .evidence-item { display: inline-block; width: 45%; margin: 1%; border: 1px solid #eee; text-align: center; vertical-align: top; }
        .img-fluid { width: 100%; height: auto; max-height: 200px; display: block; }
        .anonymous-text { font-style: italic; color: #7f8c8d; font-size: 10px; padding-top: 15px; }
    </style>
</head>
<body>
<x-pdf.pages />

<div class="header">
    <h2>Ficha de Identificação de Barreira</h2>
    <p><strong>Barreira:</strong> {{ $barrier->name }}</p>
    <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    <p><strong>Status:</strong> {{ $barrier->is_active ? 'Ativa' : 'Inativa' }}</p>
</div>

{{-- Seção 1: Localização --}}
<x-pdf.section-title title="1. Localização e Contexto" />
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
<x-pdf.section-title title="2. Detalhes da Ocorrência" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Nome da Barreira" :value="$barrier->name" colspan="2" />
        @php
            $prioColor = match($barrier->priority?->value) {
                'high' => '#e74c3c', 'medium' => '#f39c12', default => '#7f8c8d'
            };
        @endphp
        <x-pdf.info-item label="Prioridade" :value="'<b style=\'color:'.$prioColor.'\'>'.($barrier->priority?->label() ?? '---').'</b>'" />
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
<x-pdf.section-title title="3. Público-Alvo Afetado" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Deficiências Relacionadas" :value="$barrier->deficiencies->pluck('name')->join(', ') ?: 'Geral / Não especificado'" colspan="4" />
    </x-pdf.row>
</x-pdf.table>

{{-- Seção 4: Última Vistoria --}}
<x-pdf.section-title title="4. Última Vistoria" />

@php
    $lastInspection = $barrier->inspections
        ->sortByDesc('inspection_date')
        ->first();
@endphp

@if($lastInspection)
    {{-- Tabela apenas para os dados textuais --}}
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item
                label="Data e Descrição"
                :value="$lastInspection->inspection_date->format('d/m/Y') . ' - ' . ($lastInspection->description ?: 'Sem descrição')"
                colspan="1"
            />
            <x-pdf.info-item
                label="Estado da Barreira"
                :value="$lastInspection->status?->label() ?? '---'"
                colspan="1"
            />
        </x-pdf.row>
    </x-pdf.table>

    {{-- Container de Imagens FORA da tabela para permitir quebra de página --}}
    <div style="width: 100%; border: 1px solid #ccc; border-top: none; padding: 10px; background: #fff;">
        <span class="label" style="display: block; margin-bottom: 10px; font-weight: bold; font-size: 10px; color: #555;">
            IMAGENS DA VISTORIA (EVIDÊNCIAS)
        </span>

        <div style="width: 100%;">
            @if($lastInspection->images->count() > 0)
                @foreach($lastInspection->images as $image)
                    @php
                        $path = public_path('storage/' . $image->path);
                    @endphp
                    {{-- O segredo está no 'page-break-inside: avoid' para não cortar uma imagem ao meio --}}
                    <div style="display: inline-block; width: 45%; margin: 1%; border: 1px solid #eee; vertical-align: top; background: #f9f9f9; page-break-inside: avoid;">
                        @if(file_exists($path))
                            <img src="{{ $path }}" style="width: 100%; height: auto; display: block; margin: 0 auto;">
                        @else
                            <div style="padding: 20px; text-align: center; font-size: 8px; color: #999;">Imagem não encontrada</div>
                        @endif
                    </div>
                @endforeach
            @else
                <span class="value">Nenhuma imagem registrada.</span>
            @endif
        </div>
        {{-- Limpa o float/inline-block para o conteúdo seguinte --}}
        <div style="clear: both;"></div>
    </div>
@else
    <x-pdf.text-area label="Última Vistoria" :value="'Nenhuma vistoria técnica registrada até o momento.'" />
@endif

<div style="margin-top: 60px;">
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
