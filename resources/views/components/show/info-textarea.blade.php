@props([
    'label',
    'value' => null,
    'column' => 'col-md-6',
])

<div {{ $attributes->merge(['class' => $column . ' mb-4 px-4']) }}>
    <label class="d-block fw-bold text-title small mb-1" style="text-transform: uppercase;">{{ $label }}</label>
    <div class="custom-display-box-textarea">{{ $slot->isNotEmpty() ? $slot : ($value ?? '---') }}</div>
</div>