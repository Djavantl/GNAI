@props([
    'label' => null,
    'showLabel' => false
])

<div class="filter-group">

    @if($label && $showLabel)
        <label class="search-label">
            {{ $label }}
        </label>
    @endif

    {{ $slot }}

</div>
