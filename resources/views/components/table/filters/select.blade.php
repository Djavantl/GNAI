@props([
    'name',
    'options' => [],
    'label' => 'Filtrar por semestre'
])

<select
    name="{{ $name }}"
    id="{{ $name }}"
    class="semester-filter"
    data-filter-input
    aria-label="{{ $label }}"
>
    @foreach($options as $value => $labelOption)
        <option value="{{ $value }}"
            @selected(request()->query($name) === (string) $value)>
            {{ $labelOption }}
        </option>
    @endforeach
</select>
