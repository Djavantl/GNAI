<div class="custom-table-card overflow-hidden">
    @php
        $method = strtoupper($attributes->get('method', 'POST'));
    @endphp

    <form {{ $attributes }} class="p-0">
        @if($method !== 'GET')
            @csrf
        @endif

        <div class="row g-0">
            {{ $slot }}
        </div>
    </form>
</div>
