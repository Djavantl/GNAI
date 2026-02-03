@props([
    'name',
    'label',
    'value' => 1,
    'checked' => false,
    'description' => null
])

<div {{ $attributes->merge(['class' => 'custom-checkbox-wrapper']) }}>
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $id ?? $name }}" {{-- Usar um ID único é importante --}}
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        class="form-check-input custom-checkbox"
    >
    <label class="form-check-label" for="{{ $id ?? $name }}">
        <span class="fw-bold text-purple-dark">{{ $label }}</span>
        @if($description)
            <small class="d-block text-muted" style="font-size: 0.75rem;">{{ $description }}</small>
        @endif
    </label>
</div>
