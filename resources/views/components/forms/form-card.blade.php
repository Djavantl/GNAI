<div class="custom-table-card overflow-hidden">
    @php
        $method = strtoupper($attributes->get('method', 'POST'));
        $formMethod = in_array($method, ['GET', 'POST']) ? $method : 'POST';
    @endphp

    <form {{ $attributes->merge(['method' => $formMethod]) }} class="p-0">

        @if($formMethod !== 'GET')
            @csrf
        @endif

        @if(! in_array($method, ['GET','POST']))
            @method($method)
        @endif

        <div class="row g-0">
            {{ $slot }}
        </div>
    </form>
</div>
