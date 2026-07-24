@props(['label', 'value'])
<div class="label">{{ $label }}:</div>
<div class="text-box break-word">
    {!! \App\Support\RichTextSanitizer::sanitize((string) ($value ?? 'Nada declarado.')) !!}
</div>
