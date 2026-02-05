@props([
    'label',
    'value' => null,
    'column' => 'col-md-6',
    'isBox' => false // Nova prop para ativar a borda
])

<div {{ $attributes->merge(['class' => $column . ' mb-4 px-4']) }}>
    <label class="d-block fw-bold text-title small mb-1" style="text-transform: uppercase;">
        {{ $label }}
    </label>
    
    @if($isBox)
        <div class="custom-display-box">
            {{ $slot->isNotEmpty() ? $slot : ($value ?? '---') }}
        </div>
    @else
        <div class="text-base" style="color: var(--text-purple-dark); font-size: 1.05rem;">
            {{ $slot->isNotEmpty() ? $slot : ($value ?? '---') }}
        </div>
    @endif
</div>