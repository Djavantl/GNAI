@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'resourceObjects' => null,
    'search' => false,
    'required' => false
])

@php
    $elementId = $attributes->get('id') ?? $name;
@endphp

<div {{ $attributes->except('id')->merge(['class' => 'mb-3']) }}>
    @if($label)
        <label for="{{ $elementId }}" class="form-label fw-bold text-purple-dark">
            {{ $label }}
            @if($required)
                <span class="text-danger" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $elementId }}"
        @if($required) aria-required="true" @endif
        {{ $attributes->merge([
            'class' => 'form-select custom-input ' .
                       ($search ? 'select-search ' : '') .
                       ($errors->has($name) ? ' is-invalid ' : '')
        ]) }}
    >
        <option value="" {{ empty(old($name, $selected)) ? 'selected' : '' }}>
            Selecione uma opção...
        </option>

        @foreach($options as $value => $labelOption)
            @php
                $isDigital = false;
                if ($resourceObjects && $value) {
                    $item = $resourceObjects->firstWhere('id', $value);
                    $isDigital = $item && isset($item->is_digital) && $item->is_digital;
                }
            @endphp

            <option
                value="{{ $value }}"
                data-digital="{{ $isDigital ? '1' : '0' }}"
                {{ (string) old($name, $selected) === (string) $value ? 'selected' : '' }}
            >
                {{ $labelOption }}
            </option>
        @endforeach
    </select>

    @error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>
