@props([
    'fields' => []
])

<form {{ $attributes->merge([
    'class' => 'search-wrapper'
]) }}>

    <div class="search-filters-row">

        @foreach($fields as $field)

            <x-table.filters.group
                :label="$field['label'] ?? null"
            >

                @if(($field['type'] ?? 'text') === 'select')
                    <x-table.filters.select
                        :name="$field['name']"
                        :options="$field['options']"
                    />
                @else
                    <x-table.filters.input
                        :name="$field['name']"
                        :placeholder="$field['placeholder'] ?? ''"
                    />
                @endif

            </x-table.filters.group>

        @endforeach

        <x-table.filters.clear />

    </div>

</form>
