<div class="custom-table-card overflow-hidden">
    <form {{ $attributes }} class="p-0">
        @csrf
        <div class="row g-0">
            {{ $slot }}
        </div>
    </form>
</div>
