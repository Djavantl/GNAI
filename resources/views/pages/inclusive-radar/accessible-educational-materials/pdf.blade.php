<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - {{ $material->name }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
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
<x-pdf.section-title title="1. Identificação do Material" />

<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item
            label="Nome do Material"
            :value="$material->name"
            colspan="2"
        />

        <x-pdf.info-item
            label="Categoria / Tipo"
            :value="$material->type->name ?? '---'"
            colspan="2"
        />
    </x-pdf.row>

    <x-pdf.row>
        <x-pdf.info-item
            label="Patrimônio / Tombamento"
            :value="$material->asset_code ?? '---'"
            colspan="2"
        />

        <x-pdf.info-item
            label="Quantidade"
            :value="$material->quantity"
            colspan="2"
        />
    </x-pdf.row>
</x-pdf.table>

<x-pdf.text-area
    label="Descrição"
    :value="$material->notes"
/>


{{--2. Especificações Técnicas--}}
@if(count($attributeValues) > 0)
    <x-pdf.section-title title="2. Especificações Técnicas" />

    <x-pdf.table>

        @php
            $chunks = collect($attributeValues)->chunk(3);
        @endphp

        @foreach($chunks as $chunk)

            @php
                $count = $chunk->count();

                $colspan = match ($count) {
                    1 => 3,
                    2 => 1.5,
                    default => 1
                };
            @endphp

            <x-pdf.row>

                @foreach($chunk as $attributeId => $value)

                    @php
                        $attributeLabel = $material->attributeValues
                            ->firstWhere('attribute_id', $attributeId)
                            ?->attribute->label ?? '---';
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

{{-- 3. Acessibilidade e Público --}}
<x-pdf.section-title title="3. Acessibilidade e Público" />

<x-pdf.table>
    <x-pdf.row>

        <x-pdf.info-item
            label="Público-Alvo"
            :value="$material->deficiencies->pluck('name')->join(', ') ?: '---'"
            colspan="2"
        />

        <x-pdf.info-item
            label="Recursos de Acessibilidade"
            :value="$material->accessibilityFeatures->pluck('name')->join(', ') ?: '---'"
            colspan="2"
        />

    </x-pdf.row>

    <x-pdf.row>

        <x-pdf.info-item
            label="Status do Recurso"
            :value="$material->resourceStatus->name ?? '---'"
            colspan="2"
        />

        <x-pdf.info-item
            label="Tipo"
            :value="$material->type->name ?? '---'"
            colspan="2"
        />

    </x-pdf.row>

</x-pdf.table>

{{-- 4. Última Vistoria --}}
<x-pdf.section-title title="4. Última Vistoria" />

@php
    $lastInspection = $material->inspections
        ->sortByDesc('inspection_date')
        ->first();
@endphp

@if($lastInspection)

    <x-pdf.table style="border: 1px solid #ccc; margin-bottom: 10px; border-radius: 4px;">

        <x-pdf.row>
            <x-pdf.info-item
                :label="$lastInspection->inspection_date->format('d/m/Y') . ' - ' . ($lastInspection->type?->label() ?? '---')"
                :value="$lastInspection->description ?: 'Nada declarado.'"
            />

            <x-pdf.info-item
                label="Estado de Conservação"
                :value="$lastInspection->state?->label() ?? '---'"
            />
        </x-pdf.row>


        {{-- Imagens --}}
        @php
            $inspectionImagesHtml = '';

            if ($lastInspection->images->count() > 0) {
                foreach ($lastInspection->images as $image) {

                    $path = storage_path('app/public/' . $image->path);

                    $inspectionImagesHtml .= '
                        <div style="display:inline-block; width:48%; margin:1%;">
                            <img src="' . $path . '" style="width:100%; height:auto;" />
                        </div>';
                }
            } else {
                $inspectionImagesHtml = 'Sem imagem.';
            }
        @endphp

        <x-pdf.info-item
            label="Imagens da Vistoria"
            colspan="3"
            :value="$inspectionImagesHtml"
            :isHtml="true"
        />

    </x-pdf.table>

@else

    <x-pdf.text-area
        label="Última Vistoria"
        :value="'Nenhuma vistoria registrada.'"
    />

@endif


<x-pdf.pages />

</body>
</html>
