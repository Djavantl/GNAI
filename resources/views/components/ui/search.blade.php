@props([
    'url',
    'placeholder' => 'Buscar...',
    'target' => '#table-container',
    'semester' => false,
    'semesters' => collect()
])

<div class="search-wrapper">

    <div class="search-filters-row">

        @if(!empty($semester) && $semester === true)
            <select
                class="semester-filter"
                data-url="{{ $url }}"
                data-target="{{ $target }}"
            >
                <option value="">Todos os semestres</option>

                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}">
                        {{ $sem->label }}
                    </option>
                @endforeach
            </select>
        @endif

        <div class="search-box">
            <span class="search-icon">
                <i class="fas fa-search"></i>
            </span>

            <input
                type="text"
                placeholder="{{ $placeholder }}"
                data-url="{{ $url }}"
                data-target="{{ $target }}"
                class="realtime-filter search-input"
            >
        </div>

    </div>

</div>
