@props([
    'evidence',
    'height' => '250px',
    'compact' => false,
    'priority' => false,
])

@php
    $url = asset('storage/' . $evidence->path);
    $name = $evidence->displayName();
    $isImage = $evidence->isImage();
    $icon = $evidence->iconClass();
    $shortName = \Illuminate\Support\Str::limit($name, $compact ? 18 : 42);
@endphp

<a href="{{ $url }}"
   target="_blank"
   class="d-flex align-items-center justify-content-center border rounded overflow-hidden shadow-sm bg-light text-decoration-none text-secondary"
   aria-label="Abrir evidência {{ $name }}"
   style="{{ $compact ? 'width:70px;height:70px;' : 'height:' . $height . ';' }}"
>
    @if($isImage)
        <img src="{{ $url }}"
             class="{{ $compact ? 'rounded border shadow-sm' : 'w-100' }}"
             alt="Evidência {{ $name }}"
             @unless($compact)
                 width="444"
                 height="250"
                 style="height: {{ $height }}; object-fit: cover; transition: transform 0.3s;"
             @else
                 style="width:100%; height:100%; object-fit:cover;"
             @endunless
             @if($priority) fetchpriority="high" loading="eager" @else loading="lazy" @endif
        >
    @else
        <div class="d-flex flex-column align-items-center justify-content-center text-center p-2 w-100 h-100">
            <i class="fas {{ $icon }} {{ $compact ? 'fa-lg' : 'fa-3x' }} mb-2" aria-hidden="true"></i>
            <span class="small text-break">{{ $shortName }}</span>
        </div>
    @endif
</a>
