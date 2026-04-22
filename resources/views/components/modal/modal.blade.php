@props([
    'id',
    'title' => null,
    'size' => 'md',
    'centered' => true
])

<div
    {{ $attributes->merge([
        'class' => 'modal fade modal-system',
        'id' => $id,
        'tabindex' => '-1',
        'aria-hidden' => 'true',
    ]) }}
>
    <div class="modal-dialog modal-{{ $size }} {{ $centered ? 'modal-dialog-centered' : '' }}">
        <div class="modal-content">

            {{-- HEADER --}}
            @isset($header)
                <div class="modal-header">
                    <div class="modal-title">
                        {{ $header }}
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
            @elseif($title)
                <div class="modal-header">
                    <div class="modal-title">
                        {{ $title }}
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
            @endisset

            {{-- BODY --}}
            <div class="modal-body">
                {{ $slot }}
            </div>

            {{-- FOOTER --}}
            @isset($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
