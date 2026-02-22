@props([
    'id',
    'size' => 'md',
    'centered' => true
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-{{ $size }} {{ $centered ? 'modal-dialog-centered' : '' }}">
        <div class="modal-content border-0 shadow-sm rounded-3">

            {{-- HEADER --}}
            @isset($header)
                <div class="modal-header bg-purple-dark text-white border-0 px-4 py-3">
                    <div class="modal-title fs-5 fw-bold">
                        {{ $header }}
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
            @endisset

            {{-- BODY --}}
            <div class="modal-body px-4 py-4">
                {{ $slot }}
            </div>

            {{-- FOOTER --}}
            @isset($footer)
                <div class="modal-footer border-0 px-4 py-3">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
