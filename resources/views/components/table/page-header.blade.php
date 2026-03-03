@props([
    'title',
    'subtitle' => null,
    'image' => 'images/page-header3.png',
    'actionButton' => null
])

<div class="table-header" style="
    background-image: url('{{ asset($image) }}');
    background-size: auto;
    background-position: top left;
    background-repeat: repeat;
">
    <div class="table-header-overlay"></div>

    <div class="table-header-content">
        <div class="table-header-text">
            <h2>{{ $title }}</h2>
            @if($subtitle)
                <p>{{ $subtitle }}</p>
            @endif
        </div>

        {{-- Botão de ação via slot --}}
        @if($slot->isNotEmpty())
            <div class="table-header-action">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
