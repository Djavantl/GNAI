@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'resourceObjects' => null,
    'search' => false {{-- Nova Prop --}},
    'required' => false
])

<div {{ $attributes->except('id')->merge(['class' => 'mb-3']) }}>
    @if($label)
        <label for="{{ $name }}" class="form-label fw-bold text-purple-dark">{{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $attributes->get('id') ?? $name }}"
        {{ $attributes->merge([
            'class' => 'form-select custom-input ' . 
                       ($search ? 'select-search ' : '') . {{-- Classe condicional --}}
                       ($errors->has($name) ? ' is-invalid' : '')
        ]) }}
    >
        <option value="" disabled {{ empty(old($name, $selected)) ? 'selected' : '' }}>Selecione uma opção...</option>
        
        @foreach($options as $value => $labelOption)
            @php
                $isDigital = false;
                if ($resourceObjects && $value) {
                    $item = $resourceObjects->firstWhere('id', $value);
                    $isDigital = $item && $item->is_digital;
                }
            @endphp
            <option
                value="{{ $value }}"
                data-digital="{{ $isDigital ? '1' : '0' }}"
                {{ old($name, $selected) == $value ? 'selected' : '' }}
            >
                {{ $labelOption }}
            </option>
        @endforeach
    </select>
</div>