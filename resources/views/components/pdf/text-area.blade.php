<style>
    /* Força quebra de palavras e textos longos no Histórico */
    .break-word {
        word-wrap: break-word !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
        white-space: normal !important;
    }

    /* Aplicação específica para as pílulas de valores do log */
    .old-value, .new-value {
        word-wrap: break-word;
        word-break: break-word;
        overflow-wrap: anywhere;
    }
</style>

@props(['label', 'value'])
<div class="label">{{ $label }}:</div>
<div class="text-box break-word">
    {!! $value ?? 'Nada declarado.' !!}
</div>
