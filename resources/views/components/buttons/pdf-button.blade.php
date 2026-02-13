@props(['href'])

<a href="{{ $href }}" 
   target="_blank" 
   {{ $attributes->merge(['class' => 'btn-pdf-custom']) }}>
    <i class="fas fa-file-pdf"></i>

</a>

<style>
    .btn-pdf-custom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: #e02424; /* Vermelho PDF */
        color: white !important;
        padding: 8px 15px;
        border: 1px solid #000000; /* Bordinha preta fina */
        border-radius: 4px; /* Quadradinho com leve arredondamento */
        text-decoration: none;
        font-weight: bold;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-pdf-custom:hover {
        background-color: #c81e1e;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
    }

    .btn-pdf-custom i {
        font-size: 1.1rem;
    }
</style>