<!DOCTYPE html>
<html>
<head>
    <x-pdf.styles />
</head>
<body>
    <x-pdf.header
        title="Relatório Dinâmico"
        :meta="[
            'Gerado em' => now()->format('d/m/Y H:i'),
            'Registros' => count($data),
        ]"
    />

    <table class="report-table">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{!! \App\Support\RichTextSanitizer::sanitize((string) $header) !!}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach((array)$row as $value)
                        <td>{!! \App\Support\RichTextSanitizer::sanitize((string) $value) !!}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
