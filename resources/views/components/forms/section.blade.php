@props(['title'])

{{-- Usamos o merge para permitir que o 'p-0' vindo do partial funcione --}}
<div {{ $attributes->merge(['class' => 'col-12 mb-4']) }}>
    <div class="form-section-divider">
        <span class="ms-4 fw-bold" style="font-size: 1.2rem; letter-spacing: 1px;">
            {{ $title }}
        </span>
    </div>
</div>
