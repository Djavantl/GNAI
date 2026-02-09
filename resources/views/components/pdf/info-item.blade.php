@props(['label', 'value', 'colspan' => null])
<td @if($colspan) colspan="{{ $colspan }}" @endif {{ $attributes }}>
    <span class="label">{{ $label }}</span>
    <span class="value">{!! $value ?? '---' !!}</span>
</td>