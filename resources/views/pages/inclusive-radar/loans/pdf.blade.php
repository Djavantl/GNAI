<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório - Empréstimo {{ $loan->id }}</title>

    <x-pdf.styles />
</head>
<body>

<x-pdf.header
    title="Ficha de Empréstimo de Recurso"
    :status="$statusLabel"
    :meta="[
        'ID' => '#' . $loan->id,
        'Gerado em' => now()->format('d/m/Y H:i'),
    ]"
/>

{{-- 1. Identificação --}}
<x-pdf.section-title title="Identificação do Empréstimo" />

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
<x-pdf.section-title title="Recurso Emprestado" />

<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item
            label="Nome"
            :value="$loan->loanable->name ?? ($loan->loanable->title ?? '---')"
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
            :value="$loan->loanableType()->label()"
            colspan="2"
        />

        <x-pdf.info-item
            label="Quantidade"
            :value="$loan->quantity ?? 1"
            colspan="2"
        />
    </x-pdf.row>
</x-pdf.table>

{{-- 3. Envolvidos --}}
<x-pdf.section-title title="Envolvidos" />

<x-pdf.table>
    {{-- Exibe apenas se for Estudante --}}
    @if($loan->student_id)
        <x-pdf.row>
            <x-pdf.info-item
                label="Estudante (Beneficiário)"
                :value="$loan->student->person->name ?? '---'"
                colspan="2"
            />

            <x-pdf.info-item
                label="Matrícula"
                :value="$loan->student->registration ?? '---'"
                colspan="2"
            />
        </x-pdf.row>
    @endif

    {{-- Exibe apenas se for Profissional --}}
    @if($loan->professional_id)
        <x-pdf.row>
            <x-pdf.info-item
                label="Profissional (Beneficiário)"
                :value="$loan->professional->person->name ?? '---'"
                colspan="2"
            />

            <x-pdf.info-item
                label="Registro"
                :value="$loan->professional->registration ?? '---'"
                colspan="2"
            />
        </x-pdf.row>
    @endif

    <x-pdf.row>
        <x-pdf.info-item
            label="Usuário Autenticado (Responsável)"
            :value="$loan->user->name ?? '---'"
            colspan="4"
        />
    </x-pdf.row>
</x-pdf.table>

{{-- 4. Observações --}}
<x-pdf.section-title title="Observações" />

<x-pdf.text-area
    label="Histórico"
    :value="$loan->observation ?: 'Nenhuma observação registrada.'"
/>

<x-pdf.pages />

</body>
</html>
