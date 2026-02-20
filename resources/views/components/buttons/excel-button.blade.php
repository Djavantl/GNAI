{{-- resources/views/components/buttons/excel-button.blade.php --}}
@props(['href'])

<a href="{{ $href }}"
   target="_blank"
   aria-label="Exportar em Excel"
    {{ $attributes->merge(['class' => 'btn-excel-custom']) }}>
    <i class="fas fa-file-excel"></i>
</a>

<style>
    .btn-excel-custom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: #217346;
        color: white !important;
        padding: 8px 15px;
        border: 1px solid #000000;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-excel-custom:hover {
        background-color: #1b5a37;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
    }

    .btn-excel-custom i {
        font-size: 1.1rem;
    }
</style>
