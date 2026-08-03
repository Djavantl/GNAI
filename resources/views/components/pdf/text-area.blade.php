@props(['label', 'value'])
<div class="label">{{ $label }}:</div>
<div class="text-box break-word">
    {!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($value ?? 'Nada declarado.')) !!}
</div>
