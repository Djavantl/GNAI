@props([
    'name',
    'label',
    'value' => 1,
    'checked' => false,
    'description' => null,
    'id' => null,
    'required' => false
])

@php
    $checkboxId = $id ?? $name;
@endphp

<div {{ $attributes->merge(['class' => 'custom-checkbox-wrapper']) }}>
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $checkboxId }}"
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        class="form-check-input custom-checkbox"
    >

    <label class="form-check-label" for="{{ $checkboxId }}">
        <span class="fw-bold text-purple-dark">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </span>

        @if($description)
            <small class="d-block text-muted" style="font-size: 0.75rem;">
                {{ $description }}
            </small>
        @endif
    </label>
</div>
