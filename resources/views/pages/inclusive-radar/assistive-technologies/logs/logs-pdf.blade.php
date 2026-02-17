@php
    use Illuminate\Database\Eloquent\Relations\Relation;

    // 1. Resolve a classe do Model para o cabeçalho global (opcional, mas seguro)
    $mainModelClass = Relation::getMorphedModel($assistiveTechnology->getMorphClass()) ?? get_class($assistiveTechnology);

    // 2. Tenta pegar os labels traduzidos do Model, ou usa um fallback
    $modelLabels = method_exists($mainModelClass, 'getAuditLabels')
        ? $mainModelClass::getAuditLabels()
        : [];

    // 3. Mescla com os labels que já possam vir do Controller (se houver)
    $fieldLabels = array_merge($modelLabels, $fieldLabels ?? []);
@endphp

    <!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Histórico - {{ $assistiveTechnology->name }}</title>
    <style>
        {!! file_get_contents(resource_path('css/components/pdf.css')) !!}

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 11px; }
        th, td { border: 1px solid #ccc; padding: 6px; vertical-align: top; }
        th { background-color: #f5f5f5; text-align: left; }

        .arrow { font-family: DejaVu Sans, sans-serif; font-weight: bold; color: #666; }
        .old-val { color: #b02a37; text-decoration: line-through; }
        .new-val { color: #198754; font-weight: bold; }
        .field-name { font-weight: bold; color: #333; }
        ul { margin: 0; padding-left: 12px; list-style-type: none; }
        li { margin-bottom: 8px; border-bottom: 0.1pt solid #eee; padding-bottom: 4px; }
    </style>
</head>
<body>

<div class="header" style="text-align:center; margin-bottom:20px;">
    <h2>Histórico de Tecnologia Assistiva</h2>
    <p><strong>Nome:</strong>{{ $assistiveTechnology->name }}</p>
    <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
</div>

<x-pdf.section-title title="Histórico de Alterações" />

<table>
    <thead>
    <tr>
        <th style="width: 12%">Ação</th>
        <th style="width: 58%">Detalhes das Alterações</th>
        <th style="width: 15%">Usuário</th>
        <th style="width: 15%">Data</th>
    </tr>
    </thead>
    <tbody>
    @foreach($logs as $log)
        @php
            // Descobre qual o Model deste log específico (importante para casos polimórficos)
            $logModelClass = Relation::getMorphedModel($log->auditable_type) ?? $log->auditable_type;

            // Busca labels específicos deste log se forem diferentes do principal
            $currentLabels = (method_exists($logModelClass, 'getAuditLabels'))
                ? array_merge($fieldLabels, $logModelClass::getAuditLabels())
                : $fieldLabels;

            $oldValues = $log->old_values ?? [];
            $newValues = $log->new_values ?? [];
            $allFields = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

            $formatPdfValue = function ($field, $value) use ($logModelClass) {
                if (is_null($value) || $value === '' || (is_array($value) && empty($value))) return '—';

                if (class_exists($logModelClass) && method_exists($logModelClass, 'formatAuditValue')) {
                    $formatted = $logModelClass::formatAuditValue($field, $value);
                    if ($formatted !== null) return $formatted;
                }

                if (is_bool($value)) return $value ? 'Sim' : 'Não';
                if (is_array($value)) return json_encode($value, JSON_UNESCAPED_UNICODE);

                return (string) $value;
            };
        @endphp
        <tr>
            <td>
                <strong>
                    @switch($log->action)
                        @case('created') Criado @break
                        @case('updated') Atualizado @break
                        @case('deleted') Excluído @break
                        @default {{ ucfirst($log->action) }}
                    @endswitch
                </strong>
            </td>

            <td>
                @if($log->action === 'updated' && !empty($allFields))
                    <ul>
                        @foreach($allFields as $field)
                            @continue(in_array($field, ['updated_at', 'created_at', 'deleted_at']))
                            <li>
                                <span class="field-name">{{ $currentLabels[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}</span>:<br>
                                <span class="old-val">{!! $formatPdfValue($field, $oldValues[$field] ?? null) !!}</span>
                                <span class="arrow"> → </span>
                                <span class="new-val">{!! $formatPdfValue($field, $newValues[$field] ?? null) !!}</span>
                            </li>
                        @endforeach
                    </ul>
                @elseif($log->action === 'created')
                    <span style="color: #0d6efd; font-style: italic;">Registro inicializado com os dados do sistema.</span>
                @elseif($log->action === 'deleted')
                    <span style="color: #dc3545;">Registro removido permanentemente.</span>
                @else
                    —
                @endif
            </td>

            <td>{{ $log->user?->name ?? 'Sistema' }}</td>
            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<x-pdf.pages />

</body>
</html>
