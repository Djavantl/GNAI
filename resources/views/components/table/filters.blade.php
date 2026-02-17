@props([
    'fields' => []
])

<form {{ $attributes->merge(['class' => 'row row-cols-lg-auto row-cols-md-3 row-cols-sm-2 g-3 mb-4 align-items-end']) }}>

    @foreach($fields as $field)
        <div class="col-md-{{ $field['col'] ?? 3 }}">

            {{-- Label --}}
            @if(!empty($field['label']))
                <label class="form-label fw-semibold">
                    {{ $field['label'] }}
                </label>
            @endif

            @if(($field['type'] ?? 'text') === 'select')
                <select
                    name="{{ $field['name'] }}"
                    class="form-control"
                    data-filter-input
                >
                    @foreach($field['options'] as $value => $label)
                        <option value="{{ $value }}"
                            @selected(request()->query($field['name']) === (string) $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            @else
                <input
                    type="text"
                    name="{{ $field['name'] }}"
                    class="form-control"
                    placeholder="{{ $field['placeholder'] ?? '' }}"
                    value="{{ request($field['name']) }}"
                    data-filter-input
                >
            @endif

        </div>
    @endforeach

    {{-- Botão limpar --}}
    <div class="col-md-2 d-flex align-items-end">
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary w-100">
            Limpar
        </a>
    </div>

</form>

