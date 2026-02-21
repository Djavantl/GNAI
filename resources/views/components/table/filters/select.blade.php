@props([
    'name',
    'options' => []
])

<select
    name="{{ $name }}"
    class="semester-filter"
    data-filter-input
>
    @foreach($options as $value => $label)
        <option value="{{ $value }}"
            @selected(request()->query($name) === (string) $value)>
            {{ $label }}
        </option>
    @endforeach
</select>
