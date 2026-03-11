<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - {{ $training->title }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
        a { color: #4B0082; text-decoration: underline; }
    </style>
</head>
<body>
<div class="header">
    <h2>Ficha de Treinamento</h2>
    <p><strong>Título:</strong> {{ $training->title }}</p>
    <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    <p><strong>Status:</strong> {{ $training->is_active ? 'Ativo' : 'Inativo' }}</p>
</div>

{{-- Seção 1: Informações Gerais --}}
<x-pdf.section-title title="1. Informações Gerais" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item label="Título do Treinamento" :value="$training->title" colspan="2" />
        <x-pdf.info-item label="Descrição" :value="$training->description ?: '---'" colspan="2" />
    </x-pdf.row>
    <x-pdf.row>
        <x-pdf.info-item label="Vinculado a" :value="$training->trainable->name ?? '---'" colspan="2" />
        @php
            $trainableTypeLabel = match($training->trainable_type) {
                'assistive_technology' => 'Tecnologia Assistiva',
                'accessible_educational_material' => 'Material Pedagógico Acessível',
                default => '---',
            };
        @endphp

        <x-pdf.info-item label="Tipo" :value="$trainableTypeLabel" colspan="2" />
    </x-pdf.row>
</x-pdf.table>

{{-- Seção 2: Links de Tutoriais --}}
@if(!empty($training->url))
    <x-pdf.section-title title="2. Links de Tutoriais" />
    <x-pdf.table>
        @foreach($training->url as $key => $link)
            <x-pdf.row>
                <x-pdf.info-item
                    label="Tutorial {{ $key + 1 }}"
                    :value="'<a href='.$link.' target=_blank>'.$link.'</a>'"
                    colspan="4"
                    :isHtml="true"
                />
            </x-pdf.row>
        @endforeach
    </x-pdf.table>
@endif

<x-pdf.section-title title="3. Arquivos Registrados" />

@php
    $fileCount = $training->files->count();
@endphp

@if($fileCount > 0)
    <x-pdf.text-area
        label="Arquivos"
        :value="'Existem ' . $fileCount . ' documentos registrados no sistema. Para acessá-los, entre no sistema.'"
    />
    <x-pdf.table>
        @foreach($training->files as $index => $file)
            <x-pdf.row>
                <x-pdf.info-item
                    label="Arquivo {{ $index + 1 }}"
                    :value="$file->original_name ?? 'Sem nome'"
                />
            </x-pdf.row>
        @endforeach
    </x-pdf.table>
@else
    <x-pdf.text-area
        label="Arquivos"
        :value="'Nenhum arquivo registrado para este treinamento.'"
    />
@endif

<x-pdf.pages />
</body>
</html>
