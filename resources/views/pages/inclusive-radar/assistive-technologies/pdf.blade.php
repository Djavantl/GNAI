<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - {{ $assistiveTechnology->name }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
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
        @php
            $chunks = collect($attributeValues)->chunk(3);
        @endphp

        @foreach($chunks as $chunk)

            @php
                $count = $chunk->count();

                $colspan = match($count) {
                    1 => 3,
                    2 => 1.5,
                    default => 1
                };
            @endphp

            <x-pdf.row>
                @foreach($chunk as $attributeId => $value)
                    @php
                        $attributeLabel = $assistiveTechnology->attributeValues
                            ->firstWhere('attribute_id', $attributeId)?->attribute->label ?? '---';
                    @endphp

                    <x-pdf.info-item
                        :label="$attributeLabel"
                        :value="$value"
                        :colspan="$colspan"
                    />
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

{{-- Seção 4: Última Vistoria --}}
<x-pdf.section-title title="4. Última Vistoria" />

@php
    $lastInspection = $assistiveTechnology->inspections
        ->sortByDesc('inspection_date')
        ->first();
@endphp

@if($lastInspection)
    {{-- Tabela para os dados textuais --}}
    <x-pdf.table>
        <x-pdf.row>
            {{-- Coluna 1 --}}
            <x-pdf.info-item
                label="Data e Descrição"
                :value="$lastInspection->inspection_date->format('d/m/Y') . ' - ' . ($lastInspection->description ?: 'Sem descrição')"
                colspan="1"
            />
            {{-- Coluna 2 --}}
            <x-pdf.info-item
                label="Estado de Conservação"
                :value="$lastInspection->state?->label() ?? '---'"
                colspan="1"
            />
        </x-pdf.row>
    </x-pdf.table>

    {{-- Container de Imagens FORA da tabela para permitir quebra de página (Page Break) --}}
    <div style="width: 100%; border: 1px solid #ccc; border-top: none; padding: 10px; background: #fff;">
        <span class="label" style="display: block; margin-bottom: 8px; font-weight: bold; font-size: 10px;">Imagens da Vistoria</span>

        <div style="width: 100%;">
            @if($lastInspection->images->count() > 0)
                @foreach($lastInspection->images as $image)
                    @php
                        // Mantendo seu padrão de storage_path
                        $path = storage_path('app/public/' . $image->path);
                    @endphp
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
        <div style="clear: both;"></div>
    </div>
@else
    <x-pdf.text-area label="Última Vistoria" :value="'Nenhuma vistoria registrada.'" />
@endif
