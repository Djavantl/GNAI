@props(['title'])

<div {{ $attributes->merge(['class' => 'col-12 mb-4']) }} role="region">
    <div class="form-section-divider">
        <h3 class="ms-4 fw-bold mb-0" style="font-size: 1.2rem; letter-spacing: 1px; display: inline-block;">
            {{ $title }}
        </h3>
    </div>
</div>
