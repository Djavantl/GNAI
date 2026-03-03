{{-- resources/views/components/ui/section-header.blade.php --}}

@props([
    'target',            // ← nome correto
    'title',
    'description' => null,
    'startOpen' => false
])

@php
    $expanded = $startOpen ? 'true' : 'false';
    $bodyClass = $startOpen ? 'ctx-expanded' : 'ctx-collapsed';
@endphp

<div class="ctx-section">

    <div class="ctx-section-header">
        <div class="ctx-section-title">
            <h5 class="mb-0">{{ $title }}</h5>

            @if($description)
                <div class="small text-muted">
                    {{ $description }}
                </div>
            @endif
        </div>

        <button
            type="button"
            class="ctx-toggle"
            data-target="{{ $target }}"
            aria-expanded="{{ $expanded }}"
            aria-controls="{{ $target }}"
        >
            <i class="ctx-chevron fas fa-chevron-down"></i>
        </button>
    </div>

</div>