@props(['label', 'value'])
<div class="label">{{ $label }}:</div>
<div class="text-box">
    {!! $value ?? 'Nada declarado.' !!}
</div>