@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => ''
])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    @if($label)
        <label for="{{ $name }}" class="form-label fw-bold text-purple-dark">{{ $label }}</label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'form-control custom-input' . ($errors->has($name) ? ' is-invalid' : '')]) }}
    >

    @error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>
