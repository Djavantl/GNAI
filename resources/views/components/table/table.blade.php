@props([
    'headers' => [],
    'tableClass' => 'table table-hover bg-white mb-0', // Removido table-bordered
])
<div class="custom-table-card">
    <div class="table-responsive">
        <table {{ $attributes->merge(['class' => $tableClass . ' w-100']) }}>
            <thead>
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
</div>