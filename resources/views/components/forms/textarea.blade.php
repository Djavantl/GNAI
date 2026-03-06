@props([
    'name',
    'label' => null,
    'value' => '',
    'placeholder' => '',
    'rows' => 3,
    'required' => false,
    'rich' => true 
])

<div {{ $attributes->merge(['class' => 'mb-3']) }}>
    @if($label)
        <label for="{{ $name }}" class="form-label fw-bold text-purple-dark">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{-- Só aplica required no HTML se NÃO for rich editor --}}
        {{ ($required && !$rich) ? 'required' : '' }}
        aria-label="{{ $label }}"
        class="form-control custom-input {{ $errors->has($name) ? 'is-invalid' : '' }} {{ $rich ? 'rich-editor' : '' }}"
    >{{ old($name, $value) }}</textarea>

    @error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>
