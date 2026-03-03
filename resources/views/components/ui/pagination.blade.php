@props(['records'])

@php
    use Illuminate\Pagination\LengthAwarePaginator;
@endphp

@if($records instanceof LengthAwarePaginator && $records->hasPages())
    <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center bg-white custom-pagination-container">
        <div class="text-muted small fw-medium">
            Mostrando <span style="color: var(--primary-color);">{{ $records->firstItem() }}</span>
            - <span style="color: var(--primary-color);">{{ $records->lastItem() }}</span>
            de <span style="color: var(--primary-color);">{{ $records->total() }}</span>
        </div>

        <nav>
            {{ $records->links('pagination::bootstrap-4') }}
        </nav>
    </div>
@endif