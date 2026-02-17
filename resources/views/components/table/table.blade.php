@props([
    'headers' => [],
    'tableClass' => 'table table-hover bg-white mb-0',
    'records' => null
])

<div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table {{ $attributes->merge(['class' => $tableClass . ' w-100']) }}>
            <thead class="bg-light">
                <tr>
                    @foreach($headers as $header)
                        <x-table.th :class="$header['class'] ?? null">
                            {{ $header['label'] ?? $header }}
                        </x-table.th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>

   @if($records instanceof \Illuminate\Pagination\LengthAwarePaginator && $records->hasPages())
        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center bg-white custom-pagination-container">
            {{-- Texto em Português --}}
            <div class="text-muted small fw-medium">
                Mostrando <span style="color: #6f42c1;">{{ $records->firstItem() }}</span>
                - <span style="color: #6f42c1;">{{ $records->lastItem() }}</span>
                de <span style="color: #6f42c1;">{{ $records->total() }}</span>
            </div>

            <nav>
                {{-- Forçamos o template bootstrap-4 para evitar o lixo de código do tailwind/bs5 padrão --}}
                {{ $records->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    @endif
</div>
