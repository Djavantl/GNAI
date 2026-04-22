@props([
    'id',
    'title' => 'Confirmar ação',
    'message' => 'Deseja continuar com esta ação?',
    'size' => 'md',
    'confirmText' => 'Confirmar',
    'confirmVariant' => 'primary',
    'cancelText' => 'Cancelar',
    'method' => 'POST',
])

@php
    $formId = "{$id}-form";
@endphp

<x-modal :id="$id" :title="$title" :size="$size" {{ $attributes->merge(['data-confirm-modal' => 'true']) }}>
    <form action="" method="POST" class="d-flex flex-column gap-3 text-start" data-confirm-form id="{{ $formId }}">
        @csrf
        <input
            type="hidden"
            name="_method"
            value="{{ strtoupper($method) }}"
            data-confirm-method-input
            @disabled(strtoupper($method) === 'POST')
        >

        <div class="text-start">
            <p class="fw-bold mb-1" data-confirm-title>{{ $title }}</p>
            <p class="text-muted mb-0" data-confirm-message>{{ $message }}</p>
        </div>
        <div data-confirm-extra hidden></div>
    </form>

    @slot('footer')
        <x-buttons.link-button variant="secondary" data-bs-dismiss="modal">
            {{ $cancelText }}
        </x-buttons.link-button>
        <button
            type="submit"
            form="{{ $formId }}"
            class="btn-action {{ $confirmVariant }} d-inline-flex align-items-center justify-content-center"
            data-confirm-submit-button
            data-confirm-default-variant="{{ $confirmVariant }}"
        >
            <span data-confirm-submit-text>{{ $confirmText }}</span>
        </button>
    @endslot
</x-modal>
