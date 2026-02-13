<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - Empréstimo {{ $loan->id }}</title>

    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
    </style>
</head>
<body>

@php
    $currentStatus = $loan->status instanceof \App\Enums\InclusiveRadar\LoanStatus
        ? $loan->status
        : \App\Enums\InclusiveRadar\LoanStatus::tryFrom($loan->status);

    // Se estiver ativo e com atraso, sobrescreve label
    if ($currentStatus === \App\Enums\InclusiveRadar\LoanStatus::ACTIVE && $loan->due_date->isPast()) {
        $statusLabel = 'Em Atraso';
    } else {
        $statusLabel = $currentStatus?->label() ?? '---';
    }
@endphp

<div class="header">
    <h2>Ficha de Empréstimo de Recurso</h2>

    <p><strong>ID:</strong> {{ $loan->id }}</p>
    <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    <p><strong>Status:</strong> {{ $statusLabel }}</p>
</div>

{{-- 1. Identificação --}}
<x-pdf.section-title title="1. Identificação do Empréstimo" />

<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item
            label="Data de Saída"
            :value="$loan->loan_date->format('d/m/Y H:i')"
        />

        <x-pdf.info-item
            label="Previsão de Devolução"
            :value="$loan->due_date->format('d/m/Y')"
        />
    </x-pdf.row>

    <x-pdf.row>
        <x-pdf.info-item
            label="Data de Retorno"
            :value="$loan->return_date?->format('d/m/Y H:i') ?? '---'"
        />

        <x-pdf.info-item
            label="Situação"
            :value="$statusLabel"
        />
    </x-pdf.row>
</x-pdf.table>

{{-- 2. Recurso --}}
<x-pdf.section-title title="2. Recurso Emprestado" />

<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item
            label="Nome"
            :value="$loan->loanable->name ?? '---'"
            colspan="2"
        />

        <x-pdf.info-item
            label="Patrimônio"
            :value="$loan->loanable->asset_code ?? '---'"
            colspan="2"
        />
    </x-pdf.row>

    <x-pdf.row>
        <x-pdf.info-item
            label="Tipo"
            :value="class_basename($loan->loanable_type) === 'AssistiveTechnology'
                ? 'Tecnologia Assistiva'
                : 'Material Pedagógico'"
            colspan="2"
        />

        <x-pdf.info-item
            label="Categoria"
            :value="$loan->loanable->type->name ?? '---'"
            colspan="2"
        />
    </x-pdf.row>
</x-pdf.table>

{{-- 3. Envolvidos --}}
<x-pdf.section-title title="3. Envolvidos" />

<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item
            label="Estudante"
            :value="$loan->student->person->name ?? '---'"
            colspan="2"
        />

        <x-pdf.info-item
            label="Matrícula"
            :value="$loan->student->registration ?? '---'"
            colspan="2"
        />
    </x-pdf.row>

    <x-pdf.row>
        <x-pdf.info-item
            label="Profissional Responsável"
            :value="$loan->professional->person->name ?? '---'"
            colspan="2"
        />

        <x-pdf.info-item
            label="Registro"
            :value="$loan->professional->registration ?? '---'"
            colspan="2"
        />
    </x-pdf.row>
</x-pdf.table>

{{-- 4. Observações --}}
<x-pdf.section-title title="4. Observações" />

<x-pdf.text-area
    label="Histórico"
    :value="$loan->observation ?: 'Nenhuma observação registrada.'"
/>

<x-pdf.pages />

</body>
</html>
