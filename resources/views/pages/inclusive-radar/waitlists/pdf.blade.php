<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Solicitação de Fila - #{{ $waitlist->id }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}
    </style>
</head>
<body>
{{-- 1. Adicionado componente de numeração de páginas --}}
<x-pdf.pages />

<div class="header">
    <h2>Comprovante de Fila de Espera</h2>
    <p><strong>Protocolo:</strong> #{{ $waitlist->id }}</p>
    <p><strong>Emissão:</strong> {{ now()->format('d/m/Y H:i') }}</p>
</div>

{{-- Seção 1: Beneficiário --}}
<x-pdf.section-title title="1. Identificação do Solicitante" />
<x-pdf.table>
    <x-pdf.row>
        @php
            $beneficiario = 'Não informado';
            $documento = '---';
            if($waitlist->student) {
                $beneficiario = $waitlist->student->person->name;
                $documento = "Matrícula: " . $waitlist->student->registration;
            } elseif($waitlist->professional) {
                $beneficiario = $waitlist->professional->person->name;
                $documento = "Profissional";
            }
        @endphp
        <x-pdf.info-item label="Nome Completo" :value="$beneficiario" colspan="3" />
        <x-pdf.info-item label="Vínculo / Registro" :value="$documento" colspan="1" />
    </x-pdf.row>
</x-pdf.table>

{{-- Seção 2: Detalhes do Item Solicitado (Corrigida a estrutura de colunas) --}}
<x-pdf.section-title title="2. Recurso Solicitado" />
<x-pdf.table>
    <x-pdf.row>
        <x-pdf.info-item
            label="Item / Recurso"
            :value="$waitlist->waitlistable->name ?? ($waitlist->waitlistable->title ?? 'Item Removido')"
            colspan="2"
        />
        <x-pdf.info-item
            label="Data da Solicitação"
            :value="$waitlist->requested_at->format('d/m/Y')"
        />
        @php
            $statusEnum = \App\Enums\InclusiveRadar\WaitlistStatus::tryFrom($waitlist->status);
            // Usando a cor do Enum para dar um destaque visual no valor
            $statusStyle = "color: " . ($statusEnum?->color() == 'warning' ? '#856404' : '#155724');
            $statusLabel = "<span style='{$statusStyle}; font-weight: bold;'>" . ($statusEnum?->label() ?? $waitlist->status) . "</span>";
        @endphp
        <x-pdf.info-item label="Status Atual" :value="$statusLabel" />
    </x-pdf.row>
</x-pdf.table>

{{-- Seção 3: Observações --}}
<x-pdf.section-title title="3. Informações Complementares" />
<x-pdf.text-area label="Observações Técnicas" :value="$waitlist->observations ?? 'Sem observações registradas.'" />

{{-- Tabela de registro --}}
<x-pdf.table style="margin-top: 15px;">
    <x-pdf.row>
        <x-pdf.info-item label="Registrado por" :value="$waitlist->user->name ?? 'Sistema'" colspan="3" />
        <x-pdf.info-item label="Protocolo ID" :value="'#' . $waitlist->id" colspan="1" />
    </x-pdf.row>
</x-pdf.table>

{{-- 2. Melhoria na Assinatura: Usando seus componentes de assinatura em tabela --}}
<div style="margin-top: 60px;">
    <x-pdf.table-signatures>
        <x-pdf.table-signature-label label="Assinatura do Solicitante" />
        <x-pdf.table-signature-label label="Responsável pelo Setor / Carimbo" />
    </x-pdf.table-signatures>
</div>

</body>
</html>
