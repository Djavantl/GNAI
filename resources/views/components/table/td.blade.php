@props([
    'class' => 'align-middle text-base text-nowrap text-purple-light'
])
<td {{ $attributes->merge(['class' => $class]) }} style="color: var(--text-purple-light); padding: 1rem;">
    {{ $slot }}
</td>
