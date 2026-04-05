<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - {{ $event->title }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
    </style>
</head>
<body>
    <div class="header">
        <h2>Ficha de Agenda Institucional</h2>
        <p><strong>Título:</strong> {{ $event->title }}</p>
        <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        <p><strong>Status:</strong> {{ $event->is_active ? 'Ativo' : 'Inativo' }}</p>
    </div>

    {{-- Seção 1: Informações Gerais --}}
    <x-pdf.section-title title="1. Informações Gerais" />
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Título" :value="$event->title" colspan="2" />
            <x-pdf.info-item label="Descrição" :value="$event->description ?: '---'" colspan="2" />
        </x-pdf.row>
        <x-pdf.row>
            <x-pdf.info-item label="Local" :value="$event->location ?: '---'" colspan="2" />
            <x-pdf.info-item label="Organizador" :value="$event->organizer ?: '---'" colspan="2" />
        </x-pdf.row>
        <x-pdf.row>
            <x-pdf.info-item label="Ouvintes" :value="$event->audience ?: '---'" colspan="4" />
        </x-pdf.row>
    </x-pdf.table>

    {{-- Seção 2: Datas e Horários --}}
    <x-pdf.section-title title="2. Datas e Horários" />
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Data de Início" :value="$event->start_date?->format('d/m/Y')" />
            <x-pdf.info-item label="Horário de Início" :value="$event->start_time?->format('H:i')" />
            <x-pdf.info-item label="Data de Término" :value="$event->end_date?->format('d/m/Y')" />
            <x-pdf.info-item label="Horário de Término" :value="$event->end_time?->format('H:i')" />
        </x-pdf.row>
    </x-pdf.table>

    {{-- Seção 3: Status --}}
    <x-pdf.section-title title="3. Configurações de Visibilidade" />
    <x-pdf.table>
        <x-pdf.row>
            <x-pdf.info-item label="Status no Sistema" :value="$event->is_active ? 'Ativo' : 'Inativo'" colspan="4" />
        </x-pdf.row>
    </x-pdf.table>

    <x-pdf.pages />
</body>
</html>
