<table {{ $attributes->merge(['class' => 'pdf-table']) }}>
    <tbody>
        {{ $slot }}
    </tbody>
</table>