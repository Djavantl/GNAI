@props([
    'name',
    'placeholder' => ''
])

<input
    type="text"
    name="{{ $name }}"
    class="search-input"
    placeholder="{{ $placeholder }}"
    value="{{ request($name) }}"
    data-filter-input
>
