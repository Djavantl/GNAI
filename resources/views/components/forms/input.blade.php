@props([
    'name' => null,
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false
])

@php
    $cleanId = str_replace(['[', ']'], '', $name);
    $wrapperClasses = $attributes->get('class', 'mb-4');
    $inputAttributes = $attributes->except(['class']);
@endphp

<div class="{{ $wrapperClasses }}">
    @if($label)
        <label for="{{ $cleanId }}" class="form-label fw-bold text-purple-dark">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $cleanId }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        aria-label="{{ $label }}"
        @if($required) required aria-required="true" @endif
        autocomplete="off"
        {{ $inputAttributes->merge(['class' => 'form-control custom-input' . ($errors->has($name) ? ' is-invalid' : '')]) }}
    >

    @error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>
